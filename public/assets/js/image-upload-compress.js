/**
 * Resize/compress images in the browser before upload (default max 700 KB).
 * Attach to admin forms automatically; opt out with data-compress="off".
 */
(function (global) {
    'use strict';

    var metaKb = document.querySelector('meta[name="upload-max-image-kb"]');
    var MAX_BYTES = (metaKb && parseInt(metaKb.getAttribute('content'), 10) > 0)
        ? parseInt(metaKb.getAttribute('content'), 10) * 1024
        : 700 * 1024;
    var MAX_EDGE = 2200;
    var MIN_EDGE = 640;

    function formatSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' B';
        }

        return (bytes / 1024).toFixed(0) + ' KB';
    }

    function isImageFile(file) {
        if (!file || !file.type) {
            return false;
        }
        if (file.type === 'image/gif' || file.type === 'image/svg+xml') {
            return false;
        }

        return file.type.indexOf('image/') === 0;
    }

    function shouldCompress(input) {
        if (!input || input.type !== 'file') {
            return false;
        }
        if (input.dataset.compress === 'off') {
            return false;
        }
        var accept = (input.getAttribute('accept') || '').toLowerCase();
        if (accept && accept.indexOf('image') === -1) {
            return false;
        }

        return true;
    }

    function showStatus(input, text, kind) {
        var el = input._compressHint;
        if (!el) {
            el = document.createElement('div');
            el.className = 'img-compress-hint small mt-1';
            input.insertAdjacentElement('afterend', el);
            input._compressHint = el;
        }
        el.className = 'img-compress-hint small mt-1 ' + (kind === 'success'
            ? 'text-success'
            : kind === 'danger'
                ? 'text-danger'
                : 'text-muted');
        el.textContent = text;
    }

    function loadImage(file) {
        if (global.createImageBitmap) {
            return createImageBitmap(file).catch(function () {
                return loadImageViaElement(file);
            });
        }

        return loadImageViaElement(file);
    }

    function loadImageViaElement(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not read this image file.'));
            };
            img.src = url;
        });
    }

    function drawToCanvas(source, maxEdge) {
        var w = source.width;
        var h = source.height;
        if (w > maxEdge || h > maxEdge) {
            if (w >= h) {
                h = Math.round(h * maxEdge / w);
                w = maxEdge;
            } else {
                w = Math.round(w * maxEdge / h);
                h = maxEdge;
            }
        }
        var canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, w, h);
        ctx.drawImage(source, 0, 0, w, h);

        return canvas;
    }

    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) {
                resolve(blob);
            }, 'image/jpeg', quality);
        });
    }

    function releaseSource(source) {
        if (source && typeof source.close === 'function') {
            source.close();
        }
    }

    function compressFile(file, maxBytes) {
        var limit = maxBytes || MAX_BYTES;

        if (!isImageFile(file)) {
            return Promise.resolve(file);
        }
        if (file.size <= limit) {
            return Promise.resolve(file);
        }

        return loadImage(file).then(function (source) {
            var edge = MAX_EDGE;
            var quality = 0.9;
            var lastBlob = null;

            function attempt() {
                var canvas = drawToCanvas(source, edge);

                return canvasToBlob(canvas, quality).then(function (blob) {
                    lastBlob = blob;
                    if (blob && blob.size <= limit) {
                        releaseSource(source);
                        var base = (file.name || 'image').replace(/\.[^.]+$/, '') || 'image';

                        return new File([blob], base + '.jpg', {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                    }
                    if (quality > 0.52) {
                        quality -= 0.08;

                        return attempt();
                    }
                    if (edge > MIN_EDGE) {
                        edge = Math.round(edge * 0.82);
                        quality = 0.85;

                        return attempt();
                    }
                    releaseSource(source);
                    throw new Error(
                        'Could not shrink this image below ' + Math.round(limit / 1024) + ' KB. Use a smaller photo or crop it first.'
                    );
                });
            }

            return attempt();
        });
    }

    function setInputFiles(input, files) {
        var dt = new DataTransfer();
        files.forEach(function (file) {
            dt.items.add(file);
        });
        input.files = dt.files;
    }

    function filesFromInput(input) {
        return Array.prototype.slice.call(input.files || []);
    }

    function inputHasOversizedImage(input) {
        return filesFromInput(input).some(function (file) {
            return isImageFile(file) && file.size > MAX_BYTES;
        });
    }

    function processInput(input) {
        if (!shouldCompress(input)) {
            return Promise.resolve();
        }

        var files = filesFromInput(input);
        if (!files.length) {
            return Promise.resolve();
        }

        var imageFiles = files.filter(function (file) {
            return file.type === 'image/gif' || isImageFile(file);
        });

        if (!imageFiles.length) {
            return Promise.resolve();
        }

        if (imageFiles.some(function (file) { return file.type === 'image/gif'; })) {
            showStatus(input, 'GIF uploads are not auto-compressed. Please use JPG/PNG/WebP under 700 KB.', 'danger');

            return Promise.resolve();
        }

        var originalTotal = imageFiles.reduce(function (sum, file) { return sum + file.size; }, 0);
        if (!inputHasOversizedImage(input)) {
            showStatus(input, 'Ready (' + formatSize(originalTotal) + ').', 'success');

            return Promise.resolve();
        }

        showStatus(input, 'Compressing in your browser…', 'info');
        input.disabled = true;

        return Promise.all(files.map(function (file) {
            if (!isImageFile(file)) {
                return Promise.resolve(file);
            }

            return compressFile(file);
        })).then(function (processed) {
            setInputFiles(input, processed);
            var newTotal = processed.reduce(function (sum, file) { return sum + (file.size || 0); }, 0);
            showStatus(
                input,
                'Compressed to ' + formatSize(newTotal) + ' (was ' + formatSize(originalTotal) + '). Safe to upload.',
                'success'
            );
        }).catch(function (err) {
            input.value = '';
            showStatus(input, err.message || 'Compression failed.', 'danger');
        }).finally(function () {
            input.disabled = false;
        });
    }

    function bindInput(input) {
        if (!input || input.dataset.compressBound === '1') {
            return;
        }
        if (!shouldCompress(input)) {
            return;
        }
        input.dataset.compressBound = '1';
        input.addEventListener('change', function () {
            processInput(input);
        });
    }

    function scan(root) {
        var scope = root || document;
        scope.querySelectorAll('input[type="file"]').forEach(bindInput);
    }

    function observeNewFileInputs() {
        if (!global.MutationObserver) {
            return;
        }
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) {
                        return;
                    }
                    if (node.matches && node.matches('input[type="file"]')) {
                        bindInput(node);
                    }
                    if (node.querySelectorAll) {
                        scan(node);
                    }
                });
            });
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    function patchSummernoteImageUpload() {
        if (!global.jQuery || !jQuery.fn.summernote || jQuery.fn.summernote.__isangeCompressPatched) {
            return;
        }
        var original = jQuery.fn.summernote;
        jQuery.fn.summernote = function (option) {
            if (option && typeof option === 'object') {
                var config = jQuery.extend(true, {}, option);
                config.callbacks = config.callbacks || {};
                var userCallback = config.callbacks.onImageUpload;
                var $note = this;
                config.callbacks.onImageUpload = function (files) {
                    var editor = $note;
                    Array.prototype.slice.call(files).forEach(function (file) {
                        if (!isImageFile(file)) {
                            return;
                        }
                        compressFile(file).then(function (compressed) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                editor.summernote('insertImage', e.target.result, compressed.name);
                            };
                            reader.readAsDataURL(compressed);
                        }).catch(function (err) {
                            alert(err.message || 'Could not compress image for editor.');
                        });
                    });
                    if (typeof userCallback === 'function') {
                        userCallback.call(this, files);
                    }
                };
                return original.call(this, config);
            }

            return original.apply(this, arguments);
        };
        jQuery.fn.summernote.__isangeCompressPatched = true;
    }

    function boot() {
        scan(document);
        observeNewFileInputs();
    }

    patchSummernoteImageUpload();
    document.addEventListener('DOMContentLoaded', boot);

    document.addEventListener('change', function (e) {
        if (e.target && e.target.matches && e.target.matches('input[type="file"]')) {
            bindInput(e.target);
        }
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || form.dataset.compressSubmitting === '1') {
            return;
        }

        var inputs = Array.prototype.slice.call(form.querySelectorAll('input[type="file"]')).filter(shouldCompress);
        var pending = inputs.filter(inputHasOversizedImage);

        if (!pending.length) {
            return;
        }

        e.preventDefault();
        pending.forEach(function (inp) {
            showStatus(inp, 'Compressing before upload…', 'info');
        });

        Promise.all(pending.map(processInput)).then(function () {
            var stillLarge = pending.some(inputHasOversizedImage);
            if (stillLarge) {
                alert('One or more images are still too large. Choose a smaller file or wait for compression to finish.');

                return;
            }
            form.dataset.compressSubmitting = '1';
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    }, true);

    global.IsangeImageCompress = {
        MAX_BYTES: MAX_BYTES,
        compressFile: compressFile,
        processInput: processInput,
        scan: scan,
        boot: boot,
    };
})(window);

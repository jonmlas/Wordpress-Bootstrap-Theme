/**
 * insert-shortcode.js
 * Dynamic Insert Shortcode modal with repeater support.
 */
jQuery(function($) {
    // Look for localized config under several common names (adjust if you used a different var).
    var configSources = [window.shortcodes, window.zgShortcodesConfig, window.zg_shortcodes_registry];
    var shortcodesConfig = null;
    for (var i = 0; i < configSources.length; i++) {
        if (typeof configSources[i] !== 'undefined' && configSources[i]) {
            shortcodesConfig = configSources[i];
            break;
        }
    }
    if (!shortcodesConfig) {
        // No config found — nothing to do.
        return;
    }

    // Normalize config: allow array or object keyed by tag
    // If array, convert to object keyed by tag
    if (Array.isArray(shortcodesConfig)) {
        var obj = {};
        shortcodesConfig.forEach(function(sc) {
            if (sc.tag) obj[sc.tag] = sc;
        });
        shortcodesConfig = obj;
    }

    // Helper: escape HTML for values inserted into inputs
    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Create Insert Shortcode button
    function ensureInsertButton() {
        if ($('#insert-shortcode-btn').length === 0) {
            $('<button type="button" class="button" id="insert-shortcode-btn" style="margin-left:6px;">Insert Shortcode</button>')
                .insertAfter('#insert-media-button');
        }
    }

    // Create modal skeletons
    function ensureModals() {
        if ($('#zg-sc-modal').length === 0) {
            $('body').append(
                '<div id="zg-sc-modal" style="display:none;position:fixed;left:50%;top:8%;transform:translateX(-50%);z-index:99999;">' +
                    '<div style="background:#fff;padding:20px;border-radius:8px;min-width:520px;max-width:90vw;box-shadow:0 10px 30px rgba(0,0,0,0.25);">' +
                        '<h2 style="margin:0 0 12px 0;text-align:center;">Insert Shortcode</h2>' +
                        '<div id="zg-sc-list" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;"></div>' +
                        '<div style="text-align:center;margin-top:12px;"><button class="button" id="zg-sc-close">Close</button></div>' +
                    '</div>' +
                '</div>'
            );
        }

        if ($('#zg-sc-attr-modal').length === 0) {
            $('body').append(
                '<div id="zg-sc-attr-modal" style="display:none;position:fixed;left:50%;top:8%;transform:translateX(-50%);z-index:100000;">' +
                    '<div style="background:#fff;padding:20px;border-radius:8px;min-width:560px;max-width:95vw;box-shadow:0 10px 30px rgba(0,0,0,0.25);">' +
                        '<h2 id="zg-sc-attr-title" style="margin:0 0 12px 0;text-align:center;"></h2>' +
                        '<div id="zg-sc-attr-form" style="max-height:60vh;overflow:auto;padding-right:6px;"></div>' +
                        '<div style="text-align:center;margin-top:12px;">' +
                            '<button class="button button-primary" id="zg-sc-insert">Insert</button> ' +
                            '<button class="button" id="zg-sc-cancel">Cancel</button>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }
    }

    // Populate the shortcode list with priority sorting
    function populateList() {
        var $list = $('#zg-sc-list').empty();

        // Convert config to array if it's an object
        var scArray = Array.isArray(shortcodesConfig) ? shortcodesConfig : Object.values(shortcodesConfig);

        // Sort by priority ascending (default to 100 if no priority)
        scArray.sort(function(a, b) {
            var pa = (typeof a.priority === 'number') ? a.priority : 100;
            var pb = (typeof b.priority === 'number') ? b.priority : 100;
            return pa - pb;
        });

        scArray.forEach(function(sc) {
            var tag = sc.tag;
            var label = sc.label || sc.name || tag;
            var $card = $('<div style="border:1px solid #eee;padding:10px;border-radius:6px;background:#fafafa;text-align:center;">' +
                        '<a href="#" class="zg-sc-open" data-tag="' + escapeHtml(tag) + '" style="display:block;color:#0073aa;font-weight:600;text-decoration:none;">' +
                        escapeHtml(label) + '</a></div>');
            $list.append($card);
        });
    }
    
    // Build attribute form (supports text, textarea, repeater, select, color, date, time)
    function buildForm(sc) {
        var $form = $('#zg-sc-attr-form').empty();

        if (!sc || !sc.attributes || sc.attributes.length === 0) {
            $form.append('<p style="text-align:center;color:#666;">No attributes</p>');
            return;
        }

        sc.attributes.forEach(function(field) {
            var fieldType = field.type || 'text';
            var fieldName = field.attr || field.name || field.id || '';
            var fieldLabel = field.label || fieldName;
            var defaultVal = (typeof field.default !== 'undefined') ? field.default : (field.value || '');

            // Build label with optional tooltip
            var labelHtml = '<label style="display:block;font-weight:600;margin-bottom:6px;">' + escapeHtml(fieldLabel);
            if (field.tooltip) {
                // Tooltip icon with hover text and optional link
                var tooltipText = escapeHtml(field.tooltip.text || '');
                var tooltipLink = field.tooltip.link || '';
                labelHtml += ' <span style="cursor:help; color:#888; font-weight:normal;" title="' + tooltipText + '">';
                labelHtml += '&#9432;'; // info icon
                if (tooltipLink) {
                    labelHtml += ' <a href="' + tooltipLink + '" target="_blank" rel="noopener noreferrer" style="color:#06c; text-decoration:none; margin-left:4px;">&#x2197;</a>';
                }
                labelHtml += '</span>';
            }
            labelHtml += '</label>';

            if (fieldType === 'text' || fieldType === 'textarea') {
                var inputHtml = (fieldType === 'text')
                    ? '<input type="text" class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" value="' + escapeHtml(defaultVal) + '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;">'
                    : '<textarea class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" style="width:100%;min-height:100px;padding:6px;border:1px solid #ddd;border-radius:4px;">' + escapeHtml(defaultVal) + '</textarea>';
                var $row = $('<div style="margin-bottom:10px;">' + labelHtml + inputHtml + '</div>');
                $form.append($row);
            }
            else if (fieldType === 'color') {
                var colorVal = defaultVal || '#000000';
                var colorHtml = '<input type="color" class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" value="' + escapeHtml(colorVal) + '" style="width:64px;height:34px;padding:4px;border:1px solid #ddd;border-radius:4px;">';
                var $row = $('<div style="margin-bottom:10px;display:flex;align-items:center;gap:12px;">' + labelHtml + colorHtml + '</div>');
                $form.append($row);
            }
            else if (fieldType === 'date') {
                var dateVal = defaultVal || '';
                var dateHtml = '<input type="text" class="zg-field-input zg-datepicker" data-name="' + escapeHtml(fieldName) + '" value="' + escapeHtml(dateVal) + '" style="width:160px;padding:6px;border:1px solid #ddd;border-radius:4px;">';
                var $row = $('<div style="margin-bottom:10px;">' + labelHtml + dateHtml + '</div>');
                $form.append($row);
            }
            else if (fieldType === 'time') {
                var timeVal = defaultVal || '';
                var timeHtml = '<input type="time" class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" value="' + escapeHtml(timeVal) + '" style="width:160px;padding:6px;border:1px solid #ddd;border-radius:4px;">';
                var $row = $('<div style="margin-bottom:10px;">' + labelHtml + timeHtml + '</div>');
                $form.append($row);
            }
            else if (fieldType === 'checkbox') {
                var checked = (defaultVal === 'yes' || defaultVal === true) ? 'checked' : '';
                var cbHtml = '<input type="checkbox" class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" value="yes" ' + checked + '>';
                var $row = $('<div style="margin-bottom:10px;display:flex;align-items:center;gap:6px;"><label style="font-weight:600;">' + cbHtml + escapeHtml(fieldLabel) + '</label></div>');
                $form.append($row);
            }
            else if (fieldType === 'radio') {
                var opts = field.options || {};
                var $radioGroup = $('<div style="margin-bottom:10px;"><div style="font-weight:600;margin-bottom:6px;">' + escapeHtml(fieldLabel) + '</div></div>');
                Object.keys(opts).forEach(function(key) {
                    var id = 'zg-radio-' + escapeHtml(fieldName) + '-' + escapeHtml(key);
                    var checked = (key === defaultVal) ? 'checked' : '';
                    var radioHtml = '<input type="radio" id="' + id + '" class="zg-field-input" name="' + escapeHtml(fieldName) + '" data-name="' + escapeHtml(fieldName) + '" value="' + escapeHtml(key) + '" ' + checked + '> ' +
                                    '<label for="' + id + '">' + escapeHtml(opts[key]) + '</label>';
                    $radioGroup.append('<div style="margin-bottom:4px;">' + radioHtml + '</div>');
                });
                $form.append($radioGroup);
            }
            else if (fieldType === 'select') {
                var opts = field.options || [];
                var optionsHtml = '';
                if (Array.isArray(opts)) {
                    opts.forEach(function(opt) {
                        if (opt && typeof opt === 'object') {
                            var v = (typeof opt.value !== 'undefined') ? opt.value : (opt[0] || '');
                            var l = (typeof opt.label !== 'undefined') ? opt.label : (opt[1] || v);
                            optionsHtml += '<option value="' + escapeHtml(v) + '">' + escapeHtml(l) + '</option>';
                        } else {
                            optionsHtml += '<option value="' + escapeHtml(opt) + '">' + escapeHtml(opt) + '</option>';
                        }
                    });
                } else if (opts && typeof opts === 'object') {
                    for (var k in opts) {
                        if (Object.prototype.hasOwnProperty.call(opts, k)) {
                            optionsHtml += '<option value="' + escapeHtml(k) + '">' + escapeHtml(opts[k]) + '</option>';
                        }
                    }
                }
                var selHtml = '<select class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;">' +
                                optionsHtml +
                            '</select>';
                var $selRow = $('<div style="margin-bottom:10px;">' + labelHtml + selHtml + '</div>');
                $form.append($selRow);
                if (defaultVal !== '') {
                    $form.find('[data-name="' + fieldName + '"]').val(defaultVal.toString());
                }
            }
            else if (fieldType === 'repeater') {
                var repName = field.attr || field.name;
                var $rep = $('<div class="zg-repeater" data-name="' + escapeHtml(repName) + '" style="margin-bottom:12px;"></div>');
                $rep.append('<label style="display:block;font-weight:600;margin-bottom:6px;">' + escapeHtml(fieldLabel) + '</label>');
                $rep.append('<div class="zg-repeater-rows"></div>');
                var $addBtn = $('<div style="margin-top:6px;"><button type="button" class="button zg-add-row">Add ' + escapeHtml(field.item_label || 'Item') + '</button></div>');
                $rep.append($addBtn);

                function addRow(values) {
                    values = values || {};
                    var $row = $('<div class="zg-repeater-row" style="border:1px solid #e9e9e9;padding:10px;margin-bottom:8px;position:relative;border-radius:6px;background:#fff;"></div>');
                    var $remove = $('<button type="button" class="button-link zg-remove-row" style="position:absolute;right:8px;top:8px;">Remove</button>');
                    $remove.on('click', function(e){ e.preventDefault(); $row.remove(); });
                    $row.append($remove);

                    (field.fields || []).forEach(function(fld) {
                        var fType = fld.type || 'text';
                        var fName = fld.attr || fld.name;
                        var fLabel = fld.label || fName;
                        var defaultVal = (values && values[fName]) ? values[fName] : (fld.default || '');
                        if (fType === 'text') {
                            var $f = $('<div style="margin-bottom:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;">' + escapeHtml(fLabel) + '</label><input type="text" class="zg-rep-field" data-name="' + escapeHtml(fName) + '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;" value="' + escapeHtml(defaultVal) + '"></div>');
                            $row.append($f);
                        } else if (fType === 'color') {
                            var colorVal = defaultVal || '#000000';
                            var $f = $('<div style="margin-bottom:8px;display:flex;align-items:center;gap:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;min-width:100px;">' + escapeHtml(fLabel) + '</label><input type="color" class="zg-rep-field" data-name="' + escapeHtml(fName) + '" value="' + escapeHtml(colorVal) + '" style="width:56px;height:30px;padding:4px;border:1px solid #ddd;border-radius:4px;"></div>');
                            $row.append($f);
                        } else if (fType === 'date') {
                            var dateVal = defaultVal || '';
                            var $f = $('<div style="margin-bottom:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;">' + escapeHtml(fLabel) + '</label><input type="text" class="zg-rep-field zg-rep-date" data-name="' + escapeHtml(fName) + '" value="' + escapeHtml(dateVal) + '" style="width:160px;padding:6px;border:1px solid #ddd;border-radius:4px;"></div>');
                            $row.append($f);
                        } else {
                            var $f = $('<div style="margin-bottom:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;">' + escapeHtml(fLabel) + '</label><textarea class="zg-rep-field" data-name="' + escapeHtml(fName) + '" style="width:100%;min-height:100px;padding:6px;border:1px solid #ddd;border-radius:4px;">' + escapeHtml(defaultVal) + '</textarea></div>');
                            $row.append($f);
                        }
                    });

                    $rep.find('.zg-repeater-rows').append($row);

                    if ($.fn.datepicker) {
                        $row.find('.zg-rep-date').each(function() {
                            if (!$(this).hasClass('hasDatepicker')) {
                                $(this).datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true
                                });
                            }
                        });
                    }
                }
                addRow();
                $rep.on('click', '.zg-add-row', function(e) { e.preventDefault(); addRow(); });
                $form.append($rep);
            }
            else {
                var $row = $('<div style="margin-bottom:10px;">' + labelHtml + '<input type="text" class="zg-field-input" data-name="' + escapeHtml(fieldName) + '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;"></div>');
                $form.append($row);
            }
        });

        // init datepicker for top-level date fields
        if ($.fn.datepicker) {
            $form.find('.zg-datepicker').each(function() {
                if (!$(this).hasClass('hasDatepicker')) {
                    $(this).datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true
                    });
                }
            });
        }
    }






    // Helper function to format nested shortcodes with line breaks
    function formatNestedShortcodes(str) {
        if (/\]\[/.test(str)) {
            str = str
                .replace(/\]\[/g, "]\n[")          // add newline between adjacent shortcodes
                .replace(/(\[\/[^\]]+\])$/, "\n$1"); // add newline before closing tag
        }
        return str;
    }

    // Build shortcode string from form values and config (adds select + color + date + time support)
    function buildShortcodeString(tag, sc) {
        var $form = $('#zg-sc-attr-form');
        var attrs = [];
        var inner = '';

        (sc.attributes || []).forEach(function(field) {
            var fType = field.type || 'text';
            var fName = field.attr || field.name;

            if (fType === 'text' || fType === 'textarea' || fType === 'select' || fType === 'color' || fType === 'date' || fType === 'time') {
                var val = $form.find('[data-name="' + fName + '"]').val();
                if (typeof val !== 'undefined' && val !== null && String(val).trim() !== '') {
                    var safe = String(val).replace(/"/g, '&quot;');
                    attrs.push(fName + '="' + safe + '"');
                }
            }
            else if (fType === 'checkbox') {
                var checked = $form.find('[data-name="' + fName + '"]').prop('checked');
                if (checked) {
                    attrs.push(fName + '="yes"');
                }
            }
            else if (fType === 'radio') {
                var val = $form.find('[name="' + fName + '"]:checked').val();
                if (typeof val !== 'undefined' && val !== null && String(val).trim() !== '') {
                    var safe = String(val).replace(/"/g, '&quot;');
                    attrs.push(fName + '="' + safe + '"');
                }
            }
            else if (fType === 'repeater') {
                var childTag = sc.child_tag || (tag + '_item');
                var rows = $form.find('.zg-repeater[data-name="' + fName + '"] .zg-repeater-row');

                // Use map + join to add newlines only between items, no trailing newline
                inner = rows.map(function(i, row) {
                    var $row = $(row);
                    var childAttrs = [];
                    var childInner = '';

                    (field.fields || []).forEach(function(fld) {
                        var fldName = fld.attr || fld.name;
                        var fldType = fld.type || 'text';
                        var val = $row.find('[data-name="' + fldName + '"]').val() || '';
                        if (fldType === 'text' || fldType === 'color' || fldType === 'select' || fldType === 'date') {
                            if (String(val).trim() !== '') {
                                childAttrs.push(fldName + '="' + String(val).replace(/"/g, '&quot;') + '"');
                            }
                        } else {
                            childInner = val;
                        }
                    });

                    var open = '[' + childTag + (childAttrs.length ? ' ' + childAttrs.join(' ') : '') + ']';
                    var close = '[/' + childTag + ']';

                    return open + childInner + close;
                }).get().join('\n');
            }
        });

        var openParent = '[' + tag + (attrs.length ? ' ' + attrs.join(' ') : '') + ']';
        var closeParent = '[/' + tag + ']';

        // Add newline between opening tag and inner, and before closing tag
        if (inner) {
            return openParent + '\n' + inner + '\n' + closeParent;
        } else {
            return openParent + closeParent;
        }
    }

    // Replace newlines with <br> so Visual Editor shows them on separate lines
    function formatNestedShortcodesForVisualEditor(str) {
        return str.replace(/\n/g, '<br>\n');
    }


    // Insert shortcode into editor (TinyMCE or classic)
    function insertShortcode(code) {
        // Detect TinyMCE active editor
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
            // Convert newlines to <br> for better display in Visual Editor
            code = formatNestedShortcodesForVisualEditor(code);

            tinyMCE.activeEditor.execCommand('mceInsertContent', false, code);
            $('#zg-sc-attr-modal').fadeOut(120);
            return;
        }

        // Quicktags/send_to_editor or textarea fallback (keep normal newlines)
        if (typeof window.send_to_editor === 'function') {
            window.send_to_editor(code);
            $('#zg-sc-attr-modal').fadeOut(120);
            return;
        }

        var $ta = $('#content');
        if ($ta.length) {
            var el = $ta[0];
            var start = el.selectionStart || 0;
            var end = el.selectionEnd || 0;
            var val = el.value;
            el.value = val.substring(0, start) + code + val.substring(end);
            $('#zg-sc-attr-modal').fadeOut(120);
        }
    }

    // Wire events
    function wire() {
        // Open list modal
        $(document).on('click', '#insert-shortcode-btn', function(e) {
            e.preventDefault();
            populateList();
            $('#zg-sc-modal').fadeIn(160);
        });

        // Close list modal
        $(document).on('click', '#zg-sc-close', function(e) {
            e.preventDefault();
            $('#zg-sc-modal').fadeOut(120);
        });

        // Open attr modal when a shortcode is clicked
        $(document).on('click', '.zg-sc-open', function(e) {
            e.preventDefault();
            var tag = $(this).data('tag');
            var sc = shortcodesConfig[tag] || shortcodesConfig.find && shortcodesConfig.find(function(s){ return s.tag === tag; }) || null;
            $('#zg-sc-attr-title').text((sc && (sc.label || sc.name)) ? (sc.label || sc.name) : tag);
            buildForm(sc);
            $('#zg-sc-insert').data('tag', tag).data('sc', sc);
            $('#zg-sc-modal').fadeOut(120);
            $('#zg-sc-attr-modal').fadeIn(160);
        });

        // Cancel attr modal
        $(document).on('click', '#zg-sc-cancel', function(e) {
            e.preventDefault();
            $('#zg-sc-attr-modal').fadeOut(120);
        });

        // Insert button
        $(document).on('click', '#zg-sc-insert', function(e) {
            e.preventDefault();
            var tag = $(this).data('tag');
            var sc = $(this).data('sc') || shortcodesConfig[tag];
            var code = buildShortcodeString(tag, sc);
            insertShortcode(code);
        });
    }

    // Init
    ensureInsertButton();
    ensureModals();
    populateList();
    wire();
});





// Insert Accordion Shortcode in Classic Editor
jQuery(document).ready(function($) {
    $('#insert-accordion').on('click', function(e) {
        e.preventDefault();

        // Example values — replace with your repeater field values
        var accordionId = 'custom-id';
        var accordionClass = 'custom-class';
        var items = [
            { title: 'First Item', content: 'First content' },
            { title: 'Second Item', content: 'Second content' }
        ];

        // Build shortcode with indentation and line breaks
        var shortcode = '[accordion id="' + accordionId + '" class="' + accordionClass + '"]\n';
        $.each(items, function(i, item) {
            shortcode += '    [accordion_item title="' + item.title + '"]' + item.content + '[/accordion_item]\n';
        });
        shortcode += '[/accordion]';

        // Insert into Classic Editor (TinyMCE) or Textarea
        if (typeof window.tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);
        } else {
            var $textarea = $('#content');
            var cursorPos = $textarea.prop('selectionStart');
            var v = $textarea.val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);
            $textarea.val(textBefore + shortcode + textAfter);
        }
    });
});



/*! fluent-framework 1.0.7 options-compiled.js 2014-08-07 11:27:28 PM */
(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.filter = {
        filters: {},
        add: function(tag, filter) {
            (this.filters[tag] || (this.filters[tag] = [])).push(filter);
        },
        apply: function(tag, val) {
            if (this.filters[tag]) {
                var filters = this.filters[tag];
                for (var i = 0; i < filters.length; i++) {
                    val = filters[i](val);
                    if (val === false) {
                        break;
                    }
                }
            }
            return val;
        }
    };
    $.fluent.is_clone_field = function(input) {
        if (input.attr("name") && input.attr("name").indexOf("-clone##]") != -1) {
            return true;
        }
        return false;
    };
    $.fluent.layout = function() {
        if (fluent.context == "page" || fluent.context == "meta") {
            $(".wrap").addClass($(".options-layout-input:checked").val());
            $(document).on("click", ".options-layout-input", function() {
                var options_layout = $(".options-layout-input:checked").val();
                $.post(ajaxurl, {
                    action: "options_save_layout",
                    options_layout: options_layout,
                    page: pagenow,
                    screenoptionnonce: $("#screenoptionnonce").val()
                });
                if (options_layout == "options-normal") {
                    $(".wrap").addClass("options-normal").removeClass("options-block");
                } else {
                    $(".wrap").addClass("options-block").removeClass("options-normal");
                }
            });
        }
    };
    $.fluent.restore = function() {
        $(document).on("click", ".options-restore-default", function() {
            $.fluent.proccess_required = false;
            if (!confirm(fluent.L10n.warning_defaults)) {
                return false;
            }
            var input = $("<input>").attr("type", "hidden").attr("name", "restore-defaults").val("true");
            $(".wrap form").append($(input)).submit();
            $.fluent.proccess_required = true;
            return false;
        });
    };
    $.fluent.passes = true;
    $.fluent.proccess_required = true;
    $.fluent.required = function() {
        $(document).on("submit", "form", function() {
            if ($.fluent.proccess_required === false) {
                return;
            }
            $.fluent.passes = true;
            $(document).trigger("fluent/pre_validate");
            $.each(fluent.required, function(id, data) {
                $(document).trigger("fluent/validate/" + data.type, [ id, data ]);
            });
            $(document).trigger("fluent/post_validate");
            if ($.fluent.passes !== true) {
                $("#publish.button-primary-disabled").removeClass("button-primary-disabled");
                $("#publishing-action .spinner").hide();
                $(document).trigger("fluent/validation_failed");
                $("tr.options-required input").first().focus();
                return false;
            }
            $(document).trigger("fluent/validation_passed");
        });
    };
    $.fluent.required_postboxes = function() {
        if ($(".postbox").length > 0) {
            $(".postbox").each(function() {
                if ($(this).find("tr.options-required").length > 0) {
                    $(this).find("h3.hndle").addClass("options-required");
                } else {
                    $(this).find("h3.hndle").removeClass("options-required");
                }
            });
        }
    };
    $.fluent.conditionals = function() {
        $(document).on("fluent/field/change", function(e, el) {
            var changed_field = $(el).closest("tr");
            $.each(fluent.conditionals, function(index, conditional) {
                var conditional_field = $("tr#field-" + index);
                if (conditional_field.attr("id") == changed_field.attr("id")) {
                    return;
                }
                var $globalshow = false;
                $.each(conditional, function(inx, cond) {
                    var $show = true;
                    $.each(cond, function(_index, _cond) {
                        if ($("tr#field-" + _cond.id).hasClass("condition-failed")) {
                            $show = false;
                            return false;
                        }
                        var val = null;
                        val = $.fluent.filter.apply("fluent/field/" + _cond.field_type + "/value", _cond.id);
                        if ($.fluent.filter.apply("fluent/validate/" + _cond.type, {
                            value: _cond.value,
                            supplied: val
                        }) === false) {
                            $show = false;
                        }
                    });
                    if ($show === true) {
                        $globalshow = true;
                        return false;
                    }
                    $show = false;
                });
                if ($globalshow === false) {
                    conditional_field.closest("tr").fadeOut().addClass("condition-failed");
                } else {
                    $globalshow = false;
                    conditional_field.closest("tr").fadeIn().removeClass("condition-failed");
                }
            });
        });
        $(document).on("input change", "input", function() {
            $(document).trigger("fluent/field/change", $(this));
        });
    };
    $(document).ready(function() {
        $.fluent.layout();
        if (fluent.context == "page") {
            postboxes.add_postbox_toggles(pagenow);
            $.fluent.restore();
        }
        $.fluent.required();
        $.fluent.conditionals();
        $(document).on("fluent/validation_failed", function() {
            $.fluent.required_postboxes();
        });
        $(document).on("fluent/created_fields", function() {
            $(".options-spinner").each(function() {
                $(this).fadeOut("fast", function() {
                    $(this).closest("div").find(".options-table").fadeIn();
                    $(this).closest("div").find(".options-section-locked,.options-field-locked").fadeIn();
                });
            });
        });
        $(document).on("click", ".options-tabs-tabs > a", function() {
            if ($(this).hasClass("active")) {
                return false;
            }
            var target = $(this).attr("data-tab");
            var container = $(this).closest(".inside");
            container.find(".options-tabs-tabs a.active").removeClass("active");
            $(this).addClass("active");
            container.find(".options-tabs-tables .options-tab.active").removeClass("active");
            container.find(".options-tabs-tables .options-tab#" + target).addClass("active");

            //Save tab state with local storage
            var postID = $(this).parents('#post').find('input#post_ID').val();
            var parentPostBox = $(this).parents('.postbox').attr('id');
            localStorage.setItem('activeTab'+parentPostBox+postID, target);
            localStorage.setItem('parentPostBox'+parentPostBox+postID, parentPostBox);

            return false;
        });
        $(document).on("fluent/validate/text fluent/validate/password fluent/validate/email fluent/validate/url fluent/validate/number fluent/validate/textarea fluent/validate/date fluent/validate/media fluent/validate/gallery", function(e, id, data) {
            var msg = data.msg;
            if (!$.trim($(".options-table #" + id).val()).length) {
                if (!$(".options-table #" + id).closest("tr").hasClass("options-required")) {
                    $(".options-table #" + id).closest("tr").addClass("options-required");
                    $(".options-table #" + id).closest("td").append('<p class="description options-error">' + msg + "</p>");
                }
                $.fluent.passes = false;
            }
            $(document).on("input", ".options-table #" + id, function() {
                if ($.trim($(this).val()).length > 0) {
                    if ($(this).closest("tr").hasClass("options-required")) {
                        $(this).closest("tr").removeClass("options-required");
                        $("p.options-error", $(this).closest("td")).remove();
                    }
                } else {
                    if (!$(this).closest("tr").hasClass("options-required")) {
                        $(this).closest("tr").addClass("options-required");
                        $(this).closest("td").append('<p class="description options-error">' + msg + "</p>");
                    }
                }
            });
        });
        $(document).on("fluent/validate/checkbox", function(e, id, data) {
            var msg = data.msg;
            if (!$(".options-table #field-" + id + ' input[type="checkbox"]:checked').length) {
                if (!$(".options-table #field-" + id + ' input[type="checkbox"]').closest("tr").hasClass("options-required")) {
                    $(".options-table #field-" + id + ' input[type="checkbox"]').closest("tr").addClass("options-required");
                    $(".options-table #field-" + id + ' input[type="checkbox"]').closest("td").append('<p class="description options-error">This is a required field!</p>');
                }
                $.fluent.passes = false;
            }
            $(document).on("change", ".options-table #field-" + id + ' input[type="checkbox"]', function() {
                if ($('input[type="checkbox"]:checked', $(this).closest("td")).length > 0) {
                    if ($(this).closest("tr").hasClass("options-required")) {
                        $(this).closest("tr").removeClass("options-required");
                        $("p.options-error", $(this).closest("td")).remove();
                    }
                } else {
                    if (!$(this).closest("tr").hasClass("options-required")) {
                        $(this).closest("tr").addClass("options-required");
                        $(this).closest("td").append('<p class="description options-error">' + msg + "</p>");
                    }
                }
            });
        });
        $(document).on("fluent/validate/editor", function(e, id, data) {
            var msg = data.msg;
            if (typeof tinyMCE == "object") {
                tinyMCE.triggerSave();
                var eid = $("#field-" + id).find(".wp-editor-area").attr("id"), editor = tinyMCE.get(eid);
                if (editor && !editor.getContent()) {
                    if (!$(".options-table #" + id).closest("tr").hasClass("options-required")) {
                        $(".options-table #" + id).closest("tr").addClass("options-required");
                        $(".options-table #" + id).closest("td").append('<p class="description options-error">' + msg + "</p>");
                    }
                    $.fluent.passes = false;
                }
            }
        });
    });
    $(window).load(function() {
        setTimeout(function() {
            $(document).trigger("fluent/create_fields", [ $(".wrap") ]);
            $(document).trigger("fluent/created_fields", [ $(".wrap") ]);
            $(document).trigger("fluent/field/change", [ $(".wrap") ]);
            $(".wrap").find(".options-tabs-tabs,.options-section-description").fadeIn();

            var postID = $('#post').find('input#post_ID').val();
            // store active tabs to localstorage or add to first tab if no local value 
                $(".wrap").addClass(postID).find(".options-postbox").each(function(index, el) {
                    var parentPostBox = $(this).attr('id');
                var activeTab = localStorage.getItem('activeTab'+parentPostBox+postID);
                var parentPostBox = localStorage.getItem('parentPostBox'+parentPostBox+postID);

                if (activeTab != null || activeTab != undefined) {
                    
                    $('#'+activeTab).addClass('active');
                    $("[data-tab='"+activeTab+"']").addClass('active');

                } else {
                    var optTab = $(this).find('.options-tab');
                    var optTabTab = $(this).find('.options-tabs-tabs a');
                        if (optTab != undefined)
                            optTab.first().addClass('active')
                        if (optTabTab != undefined)
                            optTabTab.first().addClass('active')
                };

                });

            


        }, 10);
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.filter.add("fluent/validate/required", function(obj) {
        if (obj.supplied === "") {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/==", function(obj) {
        if (obj.value !== obj.supplied) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/!=", function(obj) {
        if (obj.value === obj.supplied) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/contains", function(obj) {
        if (obj.supplied.indexOf(obj.value) === -1) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/!contains", function(obj) {
        if (obj.supplied === "" || obj.supplied.indexOf(obj.value) >= 0) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/starts_with", function(obj) {
        if (obj.supplied.slice(0, obj.value.length) !== obj.value) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/ends_with", function(obj) {
        if (obj.supplied.slice(-obj.value.length) !== obj.value) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/>", function(obj) {
        if (obj.supplied === "" || parseInt(obj.supplied) <= parseInt(obj.value)) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/>=", function(obj) {
        if (obj.supplied === "" || parseInt(obj.supplied) < parseInt(obj.value)) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/<=", function(obj) {
        if (obj.supplied === "" || parseInt(obj.supplied) > parseInt(obj.value)) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/<", function(obj) {
        if (obj.supplied === "" || parseInt(obj.supplied) >= parseInt(obj.value)) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/between", function(obj) {
        var values = obj.value.split("|");
        if (obj.supplied === "" || parseInt(obj.supplied) < parseInt(values[0])) {
            return false;
        }
        if (parseInt(obj.supplied) > parseInt(values[1])) {
            return false;
        }
    });
    $.fluent.filter.add("fluent/validate/!between", function(obj) {
        var values = obj.value.split("|");
        if (obj.supplied === "" || parseInt(obj.supplied) > parseInt(values[0]) && parseInt(obj.supplied) < parseInt(values[1])) {
            return false;
        }
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.ace = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            if ($.fluent.is_clone_field(this.$el)) {
                return false;
            }
            var textarea = $(this.$el);
            var mode = textarea.attr("data-ace-mode");
            var theme = textarea.attr("data-ace-theme");
            var editDiv = $("<div>", {
                position: "absolute",
                width: "100%",
                height: "200px",
                "border-radius": "2px",
                "class": textarea.attr("class")
            }).insertBefore(textarea);
            textarea.css("display", "none");
            var editor = ace.edit(editDiv[0]);
            editor.renderer.setShowGutter(true);
            editor.getSession().setValue(textarea.val());
            editor.getSession().setMode("ace/mode/" + mode);
            editor.setTheme("ace/theme/" + theme);
            editor.getSession().on("change", function(e) {
                textarea.val(editor.getSession().getValue());
                $(document).trigger("fluent/field/change", $(textarea));
            });
        }
    };
    $(document).on("change", ".field-type-ace textarea", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/ace/value", function(id) {
        return $("#field-" + id).find("textarea").first().val();
    });
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find(".field-type-ace textarea").each(function() {
            $.fluent.ace.set({
                $el: $(this)
            }).init();
        });
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.background = {
        color: {
            $el: null,
            set: function(o) {
                $.extend(this, o);
                return this;
            },
            init: function() {
                if ($.fluent.is_clone_field(this.$el)) {
                    return false;
                }
                $(this.$el).wpColorPicker({
                    change: _.throttle(function() {
                        $(this).trigger("change");
                    }, 1e3)
                });
            }
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find('.field-type-background input[type="text"].color').each(function() {
            $.fluent.background.color.set({
                $el: $(this)
            }).init();
        });
    });
    $(document).on("fluent/field/change", function() {
        $(".field-type-background .options-media-id").each(function() {
            if ($(this).val() === "") {
                $(this).closest(".field-type-background").find("tr.image").fadeOut();
            } else {
                $(this).closest(".field-type-background").find("tr.image").fadeIn();
            }
        });
    });
    $(document).on("input", '.field-type-background input[type="text"].color', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("change", ".field-type-checkbox input", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/checkbox/value", function(id) {
        var val = [];
        if ($("#field-" + id).find('input[type="checkbox"]').length > 1) {
            $("#field-" + id).find('input[type="checkbox"]:checked').each(function(index, el) {
                val.push($(el).attr("name").split("][").pop().slice(0, -1));
            });
        } else {
            $("#field-" + id).find('input[type="checkbox"]:checked').each(function(index, el) {
                val.push($(el).val());
            });
        }
        return val.join("|");
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.color = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            if ($.fluent.is_clone_field(this.$el)) {
                return false;
            }
            $(this.$el).wpColorPicker({
                change: _.throttle(function() {
                    $(this).trigger("change");
                }, 1e3)
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find('.field-type-color input[type="text"]').each(function() {
            $.fluent.color.set({
                $el: $(this)
            }).init();
        });
    });
    $(document).on("input", '.field-type-color input[type="text"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/color/value", function(id) {
        return $("#field-" + id).find('input[type="text"]').first().val();
    });
    $(document).on("fluent/validate/color", function(e, id, data) {
        var msg = data.msg;
        if (!$.trim($(".options-table #" + id).val()).length) {
            if (!$(".options-table #" + id).closest("tr").hasClass("options-required")) {
                $(".options-table #" + id).closest("tr").addClass("options-required");
                $(".options-table #" + id).closest("td").append('<p class="description options-error">' + msg + "</p>");
            }
            $.fluent.passes = false;
        }
        $(document).on("click", ".options-table #field-" + id + " *", function() {
            var input = $('input[type="text"]', $(this).closest("td"));
            if ($.trim($(input).val()).length > 0) {
                if ($(this).closest("tr").hasClass("options-required")) {
                    $(this).closest("tr").removeClass("options-required");
                    $("p.options-error", $(this).closest("td")).remove();
                }
            } else {
                if (!$(this).closest("tr").hasClass("options-required")) {
                    $(this).closest("tr").addClass("options-required");
                    $(this).closest("td").append('<p class="description options-error">' + msg + "</p>");
                }
            }
        });
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.date = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            if ($.fluent.is_clone_field(this.$el)) {
                return false;
            }
            $(this.$el).datepicker({
                onSelect: function(dateText, inst) {
                    $(this).trigger("input");
                }
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find('.field-type-date input[type="text"]').each(function() {
            $.fluent.date.set({
                $el: $(this)
            }).init();
        });
    });
    $(document).on("click", ".field-type-date .dashicons", function() {
        $(this).prev("input").datepicker("show");
    });
    $(document).on("input", '.field-type-date input[type="text"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/date/value", function(id) {
        return $("#field-" + id).find('input[type="text"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("click keyup keydown focus", ".field-type-editor, .field-type-editor *", function() {
        $(document).trigger("fluent/field/change", $(this).closest("tr"));
        var eid = $(this).find("textarea.wp-editor-area").attr("id"), editor = tinyMCE.get(eid);
        if (editor) {
            editor.save();
        }
    });
    $.fluent.filter.add("fluent/field/editor/value", function(id) {
        if ($("#field-" + id).find("textarea.wp-editor-area").length > 0 && typeof tinyMCE == "object") {
            var eid = $("#field-" + id).find("textarea.wp-editor-area").attr("id"), editor = tinyMCE.get(eid);
            if (editor) {
                return editor.getContent({
                    format: "raw"
                });
            }
        }
        return $("#field-" + id).find("textarea.wp-editor-area").first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", '.field-type-email input[type="email"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/email/value", function(id) {
        return $("#field-" + id).find('input[type="email"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("click", ".field-type-export a", function() {
        window.location.href = fluent.ajax_url + "?action=fluent_export&type=" + fluent.context + "&option=" + fluent.option_name + "&rel_id=" + fluent.rel_id + "&_wpnonce=" + fluent.nonce;
        return false;
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.font = {
        color: {
            $el: null,
            set: function(o) {
                $.extend(this, o);
                return this;
            },
            init: function() {
                if ($.fluent.is_clone_field(this.$el)) {
                    return false;
                }
                $(this.$el).wpColorPicker({
                    change: _.throttle(function() {
                        $(this).trigger("change");
                    }, 1e3)
                });
            }
        },
        family: {
            $el: null,
            $instance: null,
            set: function(o) {
                $.extend(this, o);
                return this;
            },
            init: function() {
                if ($.fluent.is_clone_field(this.$el)) {
                    return false;
                }
                var select = $(this.$el).selectize({
                    options: fluent_font,
                    valueField: "name",
                    labelField: "name",
                    optgroupLabelField: "label",
                    optgroupField: "optgroup",
                    optgroups: [ {
                        value: "native",
                        label: "Native Fonts"
                    }, {
                        value: "google",
                        label: "Google Fonts"
                    } ],
                    searchField: [ "name" ],
                    create: false,
                    render: {
                        option: function(item, escape) {
                            name = item.name.replace(/\'/g, "");
                            var gfamily = item.optgroup == "google" ? ' data-gfamily="' + item.family + '"' : "";
                            return '<div class="option" style="font-family: ' + escape(item.name) + ' !important;"' + gfamily + ">" + escape(name.split(",")[0]) + "</div>";
                        },
                        item: function(item, escape) {
                            name = item.name.replace(/\'/g, "");
                            return '<div class="item" style="font-family: ' + escape(item.name) + ' !important;">' + escape(name.split(",")[0]) + "</div>";
                        }
                    },
                    onChange: function(value) {
                        $(document).trigger("fluent/field/font/preview", $(this.$el).closest(".field-type-font"));
                        $(document).trigger("fluent/field/change");
                    }
                });
                this.$instance = select.selectize;
                $(document).on("change input", "#" + $(this.$el).closest(".field-type-font").closest("tr").attr("id") + " input," + "#" + $(this.$el).closest(".field-type-font").closest("tr").attr("id") + " select", function() {
                    $(document).trigger("fluent/field/font/preview", $(this).closest(".field-type-font"));
                });
            }
        },
        loadremote: function(element) {
            var drop = $(element).closest(".selectize-control");
            $(".item[data-gfamily]:not(.loaded)", drop).each(function(index, el) {
                if (index > 3) {
                    return false;
                }
                var gfont = $(el).attr("data-gfamily");
                if (gfont !== "" && $.inArray(gfont, $.fluent.font.loadedgfonts) === -1) {
                    $("<link>").attr("rel", "stylesheet").attr("type", "text/css").attr("href", "//fonts.googleapis.com/css?family=" + gfont.replace(/ /g, "+")).appendTo("head");
                    $.fluent.font.loadedgfonts.push(gfont);
                    $(el).addClass("loaded");
                }
            });
            $(".option[data-gfamily]:not(.loaded)", drop).each(function(index, el) {
                if (index > 3) {
                    return false;
                }
                var gfont = $(el).attr("data-gfamily");
                if (gfont !== "" && $.inArray(gfont, $.fluent.font.loadedgfonts) === -1) {
                    $("<link>").attr("rel", "stylesheet").attr("type", "text/css").attr("href", "//fonts.googleapis.com/css?family=" + gfont.replace(/ /g, "+")).appendTo("head");
                    $.fluent.font.loadedgfonts.push(gfont);
                    $(el).addClass("loaded");
                }
            });
        },
        loadedgfonts: []
    };
    $(document).on("fluent/created_fields", function() {
        setTimeout(function() {
            $(".selectize-control").each(function() {
                $.fluent.font.loadremote($(this).find(".item").first());
            });
        }, 1e3);
    });
    $(document).on("fluent/field/font/preview", function() {
        var family = $("select.font-family", this).val();
        var color = $('input[type="text"].color', this).val();
        var height = $('input[type="text"].line-height', this).val();
        var size = $('input[type="text"].font-size', this).val();
        var units = $('input[type="hidden"].units', this).val();
        $(".font-preview", this).css({
            "font-family": family,
            color: color,
            "line-height": height + units,
            "font-size": size + units
        });
    });
    $(document).on("mouseenter", ".field-type-font .font-family .option", function() {
        $.fluent.font.loadremote($(this));
    });
    $(document).on("click", ".field-type-font .bg-switcher", function() {
        if ($(this).hasClass("active")) {
            $(this).closest(".font-preview").css({
                background: "#f9f9f9"
            });
            $(this).removeClass("active");
        } else {
            $(this).closest(".font-preview").css({
                background: "#333"
            });
            $(this).addClass("active");
        }
    });
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find('.field-type-font input[type="text"].color').each(function() {
            $.fluent.font.color.set({
                $el: $(this)
            }).init();
        });
        $(el).find(".field-type-font select.font-family").each(function() {
            $.fluent.font.family.set({
                $el: $(this)
            }).init();
        });
    });
    $(document).on("input", '.field-type-font input[type="text"].color', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/font/value", function(id) {
        return $("#field-" + id).find("select").first().val();
    });
    $(document).on("fluent/validate/font", function(e, id, data) {
        var msg = data.msg;
        if (!$.trim($(".options-table #" + id).val()).length) {
            if (!$(".options-table #" + id).closest("tr").hasClass("options-required")) {
                $(".options-table #" + id).closest("tr").addClass("options-required");
                $(".options-table #" + id).closest("td").append('<p class="description options-error">' + msg + "</p>");
            }
            $.fluent.passes = false;
        }
        $(document).on("click", ".options-table #field-" + id + " *", function() {
            var input = $("select", $(this).closest("td"));
            if ($.trim($(input).val()).length > 0) {
                if ($(this).closest("tr").hasClass("options-required")) {
                    $(this).closest("tr").removeClass("options-required");
                    $("p.options-error", $(this).closest("td")).remove();
                }
            } else {
                if (!$(this).closest("tr").hasClass("options-required")) {
                    $(this).closest("tr").addClass("options-required");
                    $(this).closest("td").append('<p class="description options-error">' + msg + "</p>");
                }
            }
        });
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.gallery = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            $.fluent.gallery.sortable();
            $(document).on("click", ".field-type-gallery .options-gallery-remove", function() {
                var parent = $(this).parent();
                $(".options-gallery-images div", parent).fadeOut();
                $(".options-gallery-images", parent).html("").removeClass("options-gallery-images-has-children");
                $(".options-gallery-ids", parent).val("").trigger("input");
                return false;
            });
            $(document).on("click", ".field-type-gallery .options-gallery-add-edit", function() {
                var parent = $(this).parent();
                var image_div = $(".options-gallery-images", parent);
                var input = $(".options-gallery-ids", parent);
                var val = input.val();
                var final;
                if (!val) {
                    final = '[gallery ids="0" className="test"]';
                } else {
                    final = '[gallery ids="' + val + '" className="test"]';
                }
                var frame = wp.media.gallery.edit(final);
                frame.content.get("view").sidebar.unset("gallery");
                frame.state("gallery-edit").on("update", function(selection) {
                    image_div.html("").removeClass("options-gallery-images-has-children");
                    var element, preview_html = "", preview_img;
                    var ids = selection.models.map(function(e) {
                        element = e.toJSON();
                        preview_img = typeof element.sizes.thumbnail !== "undefined" ? element.sizes.thumbnail.url : element.url;
                        preview_html = '<div class="options-gallery-thumb" data-id="' + element.id + '"><img src="' + preview_img + '" /></div>';
                        image_div.append(preview_html);
                        return e.id;
                    });
                    image_div.append('<div class="clearfix"></div>');
                    image_div.addClass("options-gallery-images-has-children");
                    input.val(ids.join(",")).trigger("input");
                    $.acm.gallery.sortable();
                });
                return false;
            });
        },
        sortable: function() {
            $(".options-gallery-images").each(function() {
                $(this).sortable({
                    update: function(event, ui) {
                        var _ids = $(this).sortable("toArray", {
                            attribute: "data-id"
                        });
                        var parent = $(this).parent();
                        $(".options-gallery-ids", parent).val(_ids.join(","));
                    }
                });
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $.fluent.gallery.init();
        $.fluent.gallery.sortable();
    });
    $(document).on("input", '.field-type-gallery input[type="hidden"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/gallery/value", function(id) {
        return $("#field-" + id).find('input[type="hidden"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.group = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {},
        sortable: function() {
            $(".options-group-table > tbody").sortable({
                containment: "parent",
                handle: ".options-group-move",
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                }
            });
        },
        table_width: function() {
            $(".options-group-table").each(function() {
                var has_actions = $("> thead > tr > th.options-group-actions", $(this)).length;
                if (has_actions > 0) {
                    var width = $(this).width() - 56;
                    $("> thead > tr > th:not(.options-group-actions)", $(this)).each(function() {
                        var pwidth = $(this).attr("data-width") - 20;
                        var nwidth = width / 100 * pwidth;
                        $(this).css("width", Math.round(nwidth) + "px");
                    });
                    width = null;
                }
            });
        },
        s4: function() {
            return Math.floor((1 + Math.random()) * 65536).toString(16).substring(1);
        },
        guid: function() {
            return this.s4() + this.s4() + "-" + this.s4() + "-" + this.s4() + "-" + this.s4() + "-" + this.s4() + this.s4() + this.s4();
        }
    };
    $(document).on("click", ".options-group-add a", function() {
        var target = $(this).attr("data-id");
        var id = "#" + target + "-template";
        var re = new RegExp("##" + target + "-clone##", "g");
        var clone = $(id).html().replace(re, "i-" + $.fluent.group.guid());
        $("#options-group-table-" + target + " > tbody").append(clone);
        if ($(this).attr("data-layout") == "horizontal") {
            $(document).trigger("fluent/create_fields", $("#options-group-table-" + target + " > tbody tr:last-child"));
            $(document).trigger("fluent/created_fields", [ $("#options-group-table-" + target + " > tbody tr:last-child") ]);
        } else if ($(this).attr("data-layout") == "vertical") {
            $(document).trigger("fluent/create_fields", $("#options-group-table-" + target + " > tbody tr:last-child").find("table"));
            $(document).trigger("fluent/created_fields", [ $("#options-group-table-" + target + " > tbody tr:last-child").find("table") ]);
        }
        $.fluent.group.sortable();
        return false;
    });
    $(document).on("click", ".options-group-remove", function() {
        $(this).closest("tr").find("td").slideUp("slow", function() {
            $(this).parent().remove();
        });
        return false;
    });
    $(document).on("fluent/created_fields", function(e, el) {
        $.fluent.group.sortable();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.import = {
        $el: null,
        $frame: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            $(document).on("click", ".field-type-import .upload", function() {
                var el = $(this).closest(".field-type-import");
                var fluent_import_frame = wp.media({
                    className: "media-frame fluent-media-frame",
                    frame: "select",
                    multiple: false,
                    title: $(this).attr("data-title"),
                    library: {
                        type: "application/wpds"
                    },
                    button: {
                        text: $(this).attr("data-select")
                    }
                });
                fluent_import_frame.on("select", function() {
                    var file = fluent_import_frame.state().get("selection").first().toJSON();
                    fluent_import_frame.close();
                    $.fluent.proccess_required = false;
                    $("#fluent-import-file").val(file.id);
                    $(".wrap form").submit();
                    $.fluent.proccess_required = true;
                    return false;
                });
                fluent_import_frame.open();
                return false;
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $.fluent.import.init();
    });
    $.fluent.filter.add("fluent/field/import/value", function(id) {
        return false;
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.info = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            if ($.fluent.is_clone_field(this.$el)) {
                return false;
            }
            $(this.$el).attr("colspan", 2).addClass("info-type-" + $(this.$el).find("div[data-info-type]").data("info-type"));
            if (fluent.context != "user") {
                $(this.$el).closest("tr").find("td.label").hide();
            } else {
                $(this.$el).closest("tr").find("th").hide();
            }
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find(".field-type-info").each(function() {
            $.fluent.info.set({
                $el: $(this)
            }).init();
        });
    });
    $(".wrap").find(".field-type-info").each(function() {
        $.fluent.info.set({
            $el: $(this)
        }).init();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.media = {
        $el: null,
        $frame: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            $(document).on("click", ".field-type-media .upload", function() {
                var el = $(this).closest(".field-type-media");
                var fluent_media_frame = wp.media({
                    className: "media-frame avm-media-frame",
                    frame: "select",
                    multiple: false,
                    title: $(this).attr("data-title"),
                    library: {},
                    button: {
                        text: $(this).attr("data-select")
                    }
                });
                fluent_media_frame.on("select", function() {
                    var media_attachment = fluent_media_frame.state().get("selection").first().toJSON();
                    var thumbSrc = media_attachment.url;
                    if (typeof media_attachment.sizes !== "undefined" && typeof media_attachment.sizes.thumbnail !== "undefined") {
                        thumbSrc = media_attachment.sizes.thumbnail.url;
                    } else if (typeof media_attachment.sizes !== "undefined") {
                        var height = media_attachment.height;
                        for (var key in media_attachment.sizes) {
                            var object = media_attachment.sizes[key];
                            if (object.height < height) {
                                height = object.height;
                                thumbSrc = object.url;
                            }
                        }
                    } else {
                        thumbSrc = fluent_media.file_icon;
                    }
                    $(".options-media-id", el).val(media_attachment.id).trigger("input");
                    $(".options-media-thumb > img", el).attr("src", thumbSrc);
                    $(".options-media-thumb > .options-media-edit", el).attr("href", media_attachment.editLink);
                    $(".upload", el).fadeOut("slow", function() {
                        $(".options-media-thumb", el).fadeIn();
                    });
                    fluent_media_frame.close();
                });
                fluent_media_frame.open();
                return false;
            });
            $(document).on("click", ".field-type-media .options-media-remove", function() {
                $(this).parent().fadeOut("slow", function() {
                    $(" img", this).attr("src", "");
                    var parent = $(this).parent();
                    $(".options-media-id", parent).val("").trigger("input");
                    $(" .upload", parent).fadeIn();
                });
                return false;
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $.fluent.media.init();
    });
    $(document).on("input", '.field-type-media input[type="hidden"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/media/value", function(id) {
        return $("#field-" + id).find('input[type="hidden"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", '.field-type-number input[type="number"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/number/value", function(id) {
        return $("#field-" + id).find('input[type="number"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", '.field-type-password input[type="password"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/password/value", function(id) {
        return $("#field-" + id).find('input[type="password"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("change click", ".field-type-radio-img *", function() {
        $(this).closest("td").find("label").removeClass("checked");
        $(this).closest("label").addClass("checked");
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/radio-img/value", function(id) {
        return $("#field-" + id).find('input[type="radio"]:checked').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("change", '.field-type-radio input[type="radio"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/radio/value", function(id) {
        return $("#field-" + id).find('input[type="radio"]:checked').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("change", ".field-type-search select", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/search/value", function(id) {
        return $("#field-" + id).find("select").first().val();
    });
    $(document).on("fluent/created_fields", function() {
        $(".options-table .selectize-search").each(function(index) {
            var valuefield = $(this).attr("data-value_key");
            var valuelabel = $(this).attr("data-value_label");
            $(this).selectize({
                valueField: valuefield,
                labelField: valuelabel,
                searchField: [ valuelabel ],
                options: [ {
                    id: 1,
                    title: "test"
                }, {
                    id: 2,
                    title: "test2"
                } ],
                create: false,
                onChange: function(value) {
                    $(document).trigger("fluent/field/change");
                },
                load: function(query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: $(this.$input[0]).attr("data-search_url"),
                        type: "POST",
                        data: {
                            q: query,
                            source: $(this.$input[0]).attr("data-source_type"),
                            post_types: $(this.$input[0]).attr("data-post_types"),
                            roles: $(this.$input[0]).attr("data-roles"),
                            key: $(this.$input[0]).attr("data-value_key"),
                            label: $(this.$input[0]).attr("data-value_label")
                        },
                        error: function() {
                            console.log("search field error");
                            callback();
                        },
                        success: function(result) {
                            var res = $.parseJSON(result);
                            console.log(res);
                            callback(res);
                        }
                    });
                }
            });
        });
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("change", ".field-type-select select", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/select/value", function(id) {
        return $("#field-" + id).find("select").first().val();
    });
    $(document).on("fluent/created_fields", function() {
        $(".options-table .selectize").selectize({
            plugins: [ "remove_button" ],
            delimiter: ",",
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                };
            },
            onChange: function(value) {
                $(document).trigger("fluent/field/change");
            }
        });
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $.fluent.switch = {
        $el: null,
        set: function(o) {
            $.extend(this, o);
            return this;
        },
        init: function() {
            if ($.fluent.is_clone_field(this.$el)) {
                return false;
            }
            $(this.$el).toggleSwitch({
                highlight: true,
                width: 30
            });
        }
    };
    $(document).on("fluent/create_fields", function(e, el) {
        $(el).find(".field-type-switch .toggle-switch").each(function() {
            $.fluent.switch.set({
                $el: $(this)
            }).init();
        });
    });
    $(document).on("change", ".field-type-switch select", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/switch/value", function(id) {
        return $("#field-" + id).find("select").first().val();
    });
    jQuery.fn.toggleSwitch = function(params) {
        var defaults = {
            highlight: true,
            width: 30,
            change: null,
            stop: null
        };
        var options = $.extend({}, defaults, params);
        return $(this).each(function(i, item) {
            generateToggle(item);
        });
        function generateToggle(selectObj) {
            var $contain = $("<div />").addClass("ui-toggle-switch");
            $(selectObj).find("option").each(function(i, item) {
                $contain.append("<label>" + $(item).text() + "</label>");
            }).end().addClass("ui-toggle-switch");
            var $slider = $("<div />").slider({
                min: 0,
                max: 100,
                animate: "fast",
                change: options.change,
                stop: function(e, ui) {
                    var roundedVal = Math.round(ui.value / 100);
                    var self = this;
                    window.setTimeout(function() {
                        toggleValue(self.parentNode, roundedVal);
                    }, 11);
                    if (typeof options.stop === "function") {
                        options.stop.call(this, e, roundedVal);
                    }
                },
                range: options.highlight && !$(selectObj).data("hideHighlight") ? "max" : null
            }).width(options.width);
            $slider.insertAfter($contain.children().eq(0));
            $contain.on("click", "label", function() {
                if ($(this).hasClass("ui-state-active")) {
                    return;
                }
                var labelIndex = $(this).is(":first-child") ? 0 : 1;
                toggleValue(this.parentNode, labelIndex);
            });
            function toggleValue(slideContain, index) {
                var $slideContain = $(slideContain), $parent = $slideContain.parent();
                $slideContain.find("label").eq(index).addClass("ui-state-active").siblings("label").removeClass("ui-state-active");
                $parent.find("option").prop("selected", false).eq(index).prop("selected", true);
                $parent.find("select").trigger("change");
                $slideContain.find(".ui-slider").slider("value", index * 52);
            }
            $contain.find("label").eq(selectObj.selectedIndex).click();
            $(selectObj).parent().append($contain);
        }
    };
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", '.field-type-text input[type="text"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/text/value", function(id) {
        return $("#field-" + id).find('input[type="text"]').first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", ".field-type-textarea textarea", function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/textarea/value", function(id) {
        return $("#field-" + id).find("textarea").first().val();
    });
})(jQuery);

(function($) {
    "use strict";
    $.fluent = $.fluent || {};
    $(document).on("input", '.field-type-url input[type="url"]', function() {
        $(document).trigger("fluent/field/change", $(this));
    });
    $.fluent.filter.add("fluent/field/url/value", function(id) {
        return $("#field-" + id).find('input[type="url"]').first().val();
    });
})(jQuery);

(function(root, factory) {
    if (typeof define === "function" && define.amd) {
        define("sifter", factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.Sifter = factory();
    }
})(this, function() {
    var Sifter = function(items, settings) {
        this.items = items;
        this.settings = settings || {
            diacritics: true
        };
    };
    Sifter.prototype.tokenize = function(query) {
        query = trim(String(query || "").toLowerCase());
        if (!query || !query.length) return [];
        var i, n, regex, letter;
        var tokens = [];
        var words = query.split(/ +/);
        for (i = 0, n = words.length; i < n; i++) {
            regex = escape_regex(words[i]);
            if (this.settings.diacritics) {
                for (letter in DIACRITICS) {
                    if (DIACRITICS.hasOwnProperty(letter)) {
                        regex = regex.replace(new RegExp(letter, "g"), DIACRITICS[letter]);
                    }
                }
            }
            tokens.push({
                string: words[i],
                regex: new RegExp(regex, "i")
            });
        }
        return tokens;
    };
    Sifter.prototype.iterator = function(object, callback) {
        var iterator;
        if (is_array(object)) {
            iterator = Array.prototype.forEach || function(callback) {
                for (var i = 0, n = this.length; i < n; i++) {
                    callback(this[i], i, this);
                }
            };
        } else {
            iterator = function(callback) {
                for (var key in this) {
                    if (this.hasOwnProperty(key)) {
                        callback(this[key], key, this);
                    }
                }
            };
        }
        iterator.apply(object, [ callback ]);
    };
    Sifter.prototype.getScoreFunction = function(search, options) {
        var self, fields, tokens, token_count;
        self = this;
        search = self.prepareSearch(search, options);
        tokens = search.tokens;
        fields = search.options.fields;
        token_count = tokens.length;
        var scoreValue = function(value, token) {
            var score, pos;
            if (!value) return 0;
            value = String(value || "");
            pos = value.search(token.regex);
            if (pos === -1) return 0;
            score = token.string.length / value.length;
            if (pos === 0) score += .5;
            return score;
        };
        var scoreObject = function() {
            var field_count = fields.length;
            if (!field_count) {
                return function() {
                    return 0;
                };
            }
            if (field_count === 1) {
                return function(token, data) {
                    return scoreValue(data[fields[0]], token);
                };
            }
            return function(token, data) {
                for (var i = 0, sum = 0; i < field_count; i++) {
                    sum += scoreValue(data[fields[i]], token);
                }
                return sum / field_count;
            };
        }();
        if (!token_count) {
            return function() {
                return 0;
            };
        }
        if (token_count === 1) {
            return function(data) {
                return scoreObject(tokens[0], data);
            };
        }
        if (search.options.conjunction === "and") {
            return function(data) {
                var score;
                for (var i = 0, sum = 0; i < token_count; i++) {
                    score = scoreObject(tokens[i], data);
                    if (score <= 0) return 0;
                    sum += score;
                }
                return sum / token_count;
            };
        } else {
            return function(data) {
                for (var i = 0, sum = 0; i < token_count; i++) {
                    sum += scoreObject(tokens[i], data);
                }
                return sum / token_count;
            };
        }
    };
    Sifter.prototype.getSortFunction = function(search, options) {
        var i, n, self, field, fields, fields_count, multiplier, multipliers, get_field, implicit_score, sort;
        self = this;
        search = self.prepareSearch(search, options);
        sort = !search.query && options.sort_empty || options.sort;
        get_field = function(name, result) {
            if (name === "$score") return result.score;
            return self.items[result.id][name];
        };
        fields = [];
        if (sort) {
            for (i = 0, n = sort.length; i < n; i++) {
                if (search.query || sort[i].field !== "$score") {
                    fields.push(sort[i]);
                }
            }
        }
        if (search.query) {
            implicit_score = true;
            for (i = 0, n = fields.length; i < n; i++) {
                if (fields[i].field === "$score") {
                    implicit_score = false;
                    break;
                }
            }
            if (implicit_score) {
                fields.unshift({
                    field: "$score",
                    direction: "desc"
                });
            }
        } else {
            for (i = 0, n = fields.length; i < n; i++) {
                if (fields[i].field === "$score") {
                    fields.splice(i, 1);
                    break;
                }
            }
        }
        multipliers = [];
        for (i = 0, n = fields.length; i < n; i++) {
            multipliers.push(fields[i].direction === "desc" ? -1 : 1);
        }
        fields_count = fields.length;
        if (!fields_count) {
            return null;
        } else if (fields_count === 1) {
            field = fields[0].field;
            multiplier = multipliers[0];
            return function(a, b) {
                return multiplier * cmp(get_field(field, a), get_field(field, b));
            };
        } else {
            return function(a, b) {
                var i, result, a_value, b_value, field;
                for (i = 0; i < fields_count; i++) {
                    field = fields[i].field;
                    result = multipliers[i] * cmp(get_field(field, a), get_field(field, b));
                    if (result) return result;
                }
                return 0;
            };
        }
    };
    Sifter.prototype.prepareSearch = function(query, options) {
        if (typeof query === "object") return query;
        options = extend({}, options);
        var option_fields = options.fields;
        var option_sort = options.sort;
        var option_sort_empty = options.sort_empty;
        if (option_fields && !is_array(option_fields)) options.fields = [ option_fields ];
        if (option_sort && !is_array(option_sort)) options.sort = [ option_sort ];
        if (option_sort_empty && !is_array(option_sort_empty)) options.sort_empty = [ option_sort_empty ];
        return {
            options: options,
            query: String(query || "").toLowerCase(),
            tokens: this.tokenize(query),
            total: 0,
            items: []
        };
    };
    Sifter.prototype.search = function(query, options) {
        var self = this, value, score, search, calculateScore;
        var fn_sort;
        var fn_score;
        search = this.prepareSearch(query, options);
        options = search.options;
        query = search.query;
        fn_score = options.score || self.getScoreFunction(search);
        if (query.length) {
            self.iterator(self.items, function(item, id) {
                score = fn_score(item);
                if (options.filter === false || score > 0) {
                    search.items.push({
                        score: score,
                        id: id
                    });
                }
            });
        } else {
            self.iterator(self.items, function(item, id) {
                search.items.push({
                    score: 1,
                    id: id
                });
            });
        }
        fn_sort = self.getSortFunction(search, options);
        if (fn_sort) search.items.sort(fn_sort);
        search.total = search.items.length;
        if (typeof options.limit === "number") {
            search.items = search.items.slice(0, options.limit);
        }
        return search;
    };
    var cmp = function(a, b) {
        if (typeof a === "number" && typeof b === "number") {
            return a > b ? 1 : a < b ? -1 : 0;
        }
        a = String(a || "").toLowerCase();
        b = String(b || "").toLowerCase();
        if (a > b) return 1;
        if (b > a) return -1;
        return 0;
    };
    var extend = function(a, b) {
        var i, n, k, object;
        for (i = 1, n = arguments.length; i < n; i++) {
            object = arguments[i];
            if (!object) continue;
            for (k in object) {
                if (object.hasOwnProperty(k)) {
                    a[k] = object[k];
                }
            }
        }
        return a;
    };
    var trim = function(str) {
        return (str + "").replace(/^\s+|\s+$|/g, "");
    };
    var escape_regex = function(str) {
        return (str + "").replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
    };
    var is_array = Array.isArray || $ && $.isArray || function(object) {
        return Object.prototype.toString.call(object) === "[object Array]";
    };
    var DIACRITICS = {
        a: "[aÀÁÂÃÄÅàáâãäå]",
        c: "[cÇçćĆčČ]",
        d: "[dđĐ]",
        e: "[eÈÉÊËèéêë]",
        i: "[iÌÍÎÏìíîï]",
        n: "[nÑñ]",
        o: "[oÒÓÔÕÕÖØòóôõöø]",
        s: "[sŠš]",
        u: "[uÙÚÛÜùúûü]",
        y: "[yŸÿý]",
        z: "[zŽž]"
    };
    return Sifter;
});

(function(root, factory) {
    if (typeof define === "function" && define.amd) {
        define("microplugin", factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.MicroPlugin = factory();
    }
})(this, function() {
    var MicroPlugin = {};
    MicroPlugin.mixin = function(Interface) {
        Interface.plugins = {};
        Interface.prototype.initializePlugins = function(plugins) {
            var i, n, key;
            var self = this;
            var queue = [];
            self.plugins = {
                names: [],
                settings: {},
                requested: {},
                loaded: {}
            };
            if (utils.isArray(plugins)) {
                for (i = 0, n = plugins.length; i < n; i++) {
                    if (typeof plugins[i] === "string") {
                        queue.push(plugins[i]);
                    } else {
                        self.plugins.settings[plugins[i].name] = plugins[i].options;
                        queue.push(plugins[i].name);
                    }
                }
            } else if (plugins) {
                for (key in plugins) {
                    if (plugins.hasOwnProperty(key)) {
                        self.plugins.settings[key] = plugins[key];
                        queue.push(key);
                    }
                }
            }
            while (queue.length) {
                self.require(queue.shift());
            }
        };
        Interface.prototype.loadPlugin = function(name) {
            var self = this;
            var plugins = self.plugins;
            var plugin = Interface.plugins[name];
            if (!Interface.plugins.hasOwnProperty(name)) {
                throw new Error('Unable to find "' + name + '" plugin');
            }
            plugins.requested[name] = true;
            plugins.loaded[name] = plugin.fn.apply(self, [ self.plugins.settings[name] || {} ]);
            plugins.names.push(name);
        };
        Interface.prototype.require = function(name) {
            var self = this;
            var plugins = self.plugins;
            if (!self.plugins.loaded.hasOwnProperty(name)) {
                if (plugins.requested[name]) {
                    throw new Error('Plugin has circular dependency ("' + name + '")');
                }
                self.loadPlugin(name);
            }
            return plugins.loaded[name];
        };
        Interface.define = function(name, fn) {
            Interface.plugins[name] = {
                name: name,
                fn: fn
            };
        };
    };
    var utils = {
        isArray: Array.isArray || function(vArg) {
            return Object.prototype.toString.call(vArg) === "[object Array]";
        }
    };
    return MicroPlugin;
});

(function(root, factory) {
    if (typeof define === "function" && define.amd) {
        define("selectize", [ "jquery", "sifter", "microplugin" ], factory);
    } else {
        root.Selectize = factory(root.jQuery, root.Sifter, root.MicroPlugin);
    }
})(this, function($, Sifter, MicroPlugin) {
    "use strict";
    var highlight = function($element, pattern) {
        if (typeof pattern === "string" && !pattern.length) return;
        var regex = typeof pattern === "string" ? new RegExp(pattern, "i") : pattern;
        var highlight = function(node) {
            var skip = 0;
            if (node.nodeType === 3) {
                var pos = node.data.search(regex);
                if (pos >= 0 && node.data.length > 0) {
                    var match = node.data.match(regex);
                    var spannode = document.createElement("span");
                    spannode.className = "highlight";
                    var middlebit = node.splitText(pos);
                    var endbit = middlebit.splitText(match[0].length);
                    var middleclone = middlebit.cloneNode(true);
                    spannode.appendChild(middleclone);
                    middlebit.parentNode.replaceChild(spannode, middlebit);
                    skip = 1;
                }
            } else if (node.nodeType === 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
                for (var i = 0; i < node.childNodes.length; ++i) {
                    i += highlight(node.childNodes[i]);
                }
            }
            return skip;
        };
        return $element.each(function() {
            highlight(this);
        });
    };
    var MicroEvent = function() {};
    MicroEvent.prototype = {
        on: function(event, fct) {
            this._events = this._events || {};
            this._events[event] = this._events[event] || [];
            this._events[event].push(fct);
        },
        off: function(event, fct) {
            var n = arguments.length;
            if (n === 0) return delete this._events;
            if (n === 1) return delete this._events[event];
            this._events = this._events || {};
            if (event in this._events === false) return;
            this._events[event].splice(this._events[event].indexOf(fct), 1);
        },
        trigger: function(event) {
            this._events = this._events || {};
            if (event in this._events === false) return;
            for (var i = 0; i < this._events[event].length; i++) {
                this._events[event][i].apply(this, Array.prototype.slice.call(arguments, 1));
            }
        }
    };
    MicroEvent.mixin = function(destObject) {
        var props = [ "on", "off", "trigger" ];
        for (var i = 0; i < props.length; i++) {
            destObject.prototype[props[i]] = MicroEvent.prototype[props[i]];
        }
    };
    var IS_MAC = /Mac/.test(navigator.userAgent);
    var KEY_A = 65;
    var KEY_COMMA = 188;
    var KEY_RETURN = 13;
    var KEY_ESC = 27;
    var KEY_LEFT = 37;
    var KEY_UP = 38;
    var KEY_RIGHT = 39;
    var KEY_DOWN = 40;
    var KEY_BACKSPACE = 8;
    var KEY_DELETE = 46;
    var KEY_SHIFT = 16;
    var KEY_CMD = IS_MAC ? 91 : 17;
    var KEY_CTRL = IS_MAC ? 18 : 17;
    var KEY_TAB = 9;
    var TAG_SELECT = 1;
    var TAG_INPUT = 2;
    var isset = function(object) {
        return typeof object !== "undefined";
    };
    var hash_key = function(value) {
        if (typeof value === "undefined" || value === null) return "";
        if (typeof value === "boolean") return value ? "1" : "0";
        return value + "";
    };
    var escape_html = function(str) {
        return (str + "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    };
    var escape_replace = function(str) {
        return (str + "").replace(/\$/g, "$$$$");
    };
    var hook = {};
    hook.before = function(self, method, fn) {
        var original = self[method];
        self[method] = function() {
            fn.apply(self, arguments);
            return original.apply(self, arguments);
        };
    };
    hook.after = function(self, method, fn) {
        var original = self[method];
        self[method] = function() {
            var result = original.apply(self, arguments);
            fn.apply(self, arguments);
            return result;
        };
    };
    var build_hash_table = function(key, objects) {
        if (!$.isArray(objects)) return objects;
        var i, n, table = {};
        for (i = 0, n = objects.length; i < n; i++) {
            if (objects[i].hasOwnProperty(key)) {
                table[objects[i][key]] = objects[i];
            }
        }
        return table;
    };
    var once = function(fn) {
        var called = false;
        return function() {
            if (called) return;
            called = true;
            fn.apply(this, arguments);
        };
    };
    var debounce = function(fn, delay) {
        var timeout;
        return function() {
            var self = this;
            var args = arguments;
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function() {
                fn.apply(self, args);
            }, delay);
        };
    };
    var debounce_events = function(self, types, fn) {
        var type;
        var trigger = self.trigger;
        var event_args = {};
        self.trigger = function() {
            var type = arguments[0];
            if (types.indexOf(type) !== -1) {
                event_args[type] = arguments;
            } else {
                return trigger.apply(self, arguments);
            }
        };
        fn.apply(self, []);
        self.trigger = trigger;
        for (type in event_args) {
            if (event_args.hasOwnProperty(type)) {
                trigger.apply(self, event_args[type]);
            }
        }
    };
    var watchChildEvent = function($parent, event, selector, fn) {
        $parent.on(event, selector, function(e) {
            var child = e.target;
            while (child && child.parentNode !== $parent[0]) {
                child = child.parentNode;
            }
            e.currentTarget = child;
            return fn.apply(this, [ e ]);
        });
    };
    var getSelection = function(input) {
        var result = {};
        if ("selectionStart" in input) {
            result.start = input.selectionStart;
            result.length = input.selectionEnd - result.start;
        } else if (document.selection) {
            input.focus();
            var sel = document.selection.createRange();
            var selLen = document.selection.createRange().text.length;
            sel.moveStart("character", -input.value.length);
            result.start = sel.text.length - selLen;
            result.length = selLen;
        }
        return result;
    };
    var transferStyles = function($from, $to, properties) {
        var i, n, styles = {};
        if (properties) {
            for (i = 0, n = properties.length; i < n; i++) {
                styles[properties[i]] = $from.css(properties[i]);
            }
        } else {
            styles = $from.css();
        }
        $to.css(styles);
    };
    var measureString = function(str, $parent) {
        var $test = $("<test>").css({
            position: "absolute",
            top: -99999,
            left: -99999,
            width: "auto",
            padding: 0,
            whiteSpace: "pre"
        }).text(str).appendTo("body");
        transferStyles($parent, $test, [ "letterSpacing", "fontSize", "fontFamily", "fontWeight", "textTransform" ]);
        var width = $test.width();
        $test.remove();
        return width;
    };
    var autoGrow = function($input) {
        var update = function(e) {
            var value, keyCode, printable, placeholder, width;
            var shift, character, selection;
            e = e || window.event || {};
            if (e.metaKey || e.altKey) return;
            if ($input.data("grow") === false) return;
            value = $input.val();
            if (e.type && e.type.toLowerCase() === "keydown") {
                keyCode = e.keyCode;
                printable = keyCode >= 97 && keyCode <= 122 || keyCode >= 65 && keyCode <= 90 || keyCode >= 48 && keyCode <= 57 || keyCode === 32;
                if (keyCode === KEY_DELETE || keyCode === KEY_BACKSPACE) {
                    selection = getSelection($input[0]);
                    if (selection.length) {
                        value = value.substring(0, selection.start) + value.substring(selection.start + selection.length);
                    } else if (keyCode === KEY_BACKSPACE && selection.start) {
                        value = value.substring(0, selection.start - 1) + value.substring(selection.start + 1);
                    } else if (keyCode === KEY_DELETE && typeof selection.start !== "undefined") {
                        value = value.substring(0, selection.start) + value.substring(selection.start + 1);
                    }
                } else if (printable) {
                    shift = e.shiftKey;
                    character = String.fromCharCode(e.keyCode);
                    if (shift) character = character.toUpperCase(); else character = character.toLowerCase();
                    value += character;
                }
            }
            placeholder = $input.attr("placeholder") || "";
            if (!value.length && placeholder.length) {
                value = placeholder;
            }
            width = measureString(value, $input) + 4;
            if (width !== $input.width()) {
                $input.width(width);
                $input.triggerHandler("resize");
            }
        };
        $input.on("keydown keyup update blur", update);
        update();
    };
    var Selectize = function($input, settings) {
        var key, i, n, dir, input, self = this;
        input = $input[0];
        input.selectize = self;
        dir = window.getComputedStyle ? window.getComputedStyle(input, null).getPropertyValue("direction") : input.currentStyle && input.currentStyle.direction;
        dir = dir || $input.parents("[dir]:first").attr("dir") || "";
        $.extend(self, {
            settings: settings,
            $input: $input,
            tagType: input.tagName.toLowerCase() === "select" ? TAG_SELECT : TAG_INPUT,
            rtl: /rtl/i.test(dir),
            eventNS: ".selectize" + ++Selectize.count,
            highlightedValue: null,
            isOpen: false,
            isDisabled: false,
            isRequired: $input.is("[required]"),
            isInvalid: false,
            isLocked: false,
            isFocused: false,
            isInputHidden: false,
            isSetup: false,
            isShiftDown: false,
            isCmdDown: false,
            isCtrlDown: false,
            ignoreFocus: false,
            ignoreHover: false,
            hasOptions: false,
            currentResults: null,
            lastValue: "",
            caretPos: 0,
            loading: 0,
            loadedSearches: {},
            $activeOption: null,
            $activeItems: [],
            optgroups: {},
            options: {},
            userOptions: {},
            items: [],
            renderCache: {},
            onSearchChange: debounce(self.onSearchChange, settings.loadThrottle)
        });
        self.sifter = new Sifter(this.options, {
            diacritics: settings.diacritics
        });
        $.extend(self.options, build_hash_table(settings.valueField, settings.options));
        delete self.settings.options;
        $.extend(self.optgroups, build_hash_table(settings.optgroupValueField, settings.optgroups));
        delete self.settings.optgroups;
        self.settings.mode = self.settings.mode || (self.settings.maxItems === 1 ? "single" : "multi");
        if (typeof self.settings.hideSelected !== "boolean") {
            self.settings.hideSelected = self.settings.mode === "multi";
        }
        self.initializePlugins(self.settings.plugins);
        self.setupCallbacks();
        self.setupTemplates();
        self.setup();
    };
    MicroEvent.mixin(Selectize);
    MicroPlugin.mixin(Selectize);
    $.extend(Selectize.prototype, {
        setup: function() {
            var self = this;
            var settings = self.settings;
            var eventNS = self.eventNS;
            var $window = $(window);
            var $document = $(document);
            var $wrapper;
            var $control;
            var $control_input;
            var $dropdown;
            var $dropdown_content;
            var $dropdown_parent;
            var inputMode;
            var timeout_blur;
            var timeout_focus;
            var tab_index;
            var classes;
            var classes_plugins;
            inputMode = self.settings.mode;
            tab_index = self.$input.attr("tabindex") || "";
            classes = self.$input.attr("class") || "";
            $wrapper = $("<div>").addClass(settings.wrapperClass).addClass(classes).addClass(inputMode);
            $control = $("<div>").addClass(settings.inputClass).addClass("items").appendTo($wrapper);
            $control_input = $('<input type="text" autocomplete="off">').appendTo($control).attr("tabindex", tab_index);
            $dropdown_parent = $(settings.dropdownParent || $wrapper);
            $dropdown = $("<div>").addClass(settings.dropdownClass).addClass(classes).addClass(inputMode).hide().appendTo($dropdown_parent);
            $dropdown_content = $("<div>").addClass(settings.dropdownContentClass).appendTo($dropdown);
            $wrapper.css({
                width: self.$input[0].style.width
            });
            if (self.plugins.names.length) {
                classes_plugins = "plugin-" + self.plugins.names.join(" plugin-");
                $wrapper.addClass(classes_plugins);
                $dropdown.addClass(classes_plugins);
            }
            if ((settings.maxItems === null || settings.maxItems > 1) && self.tagType === TAG_SELECT) {
                self.$input.attr("multiple", "multiple");
            }
            if (self.settings.placeholder) {
                $control_input.attr("placeholder", settings.placeholder);
            }
            self.$wrapper = $wrapper;
            self.$control = $control;
            self.$control_input = $control_input;
            self.$dropdown = $dropdown;
            self.$dropdown_content = $dropdown_content;
            $dropdown.on("mouseenter", "[data-selectable]", function() {
                return self.onOptionHover.apply(self, arguments);
            });
            $dropdown.on("mousedown", "[data-selectable]", function() {
                return self.onOptionSelect.apply(self, arguments);
            });
            watchChildEvent($control, "mousedown", "*:not(input)", function() {
                return self.onItemSelect.apply(self, arguments);
            });
            autoGrow($control_input);
            $control.on({
                mousedown: function() {
                    return self.onMouseDown.apply(self, arguments);
                },
                click: function() {
                    return self.onClick.apply(self, arguments);
                }
            });
            $control_input.on({
                mousedown: function(e) {
                    e.stopPropagation();
                },
                keydown: function() {
                    return self.onKeyDown.apply(self, arguments);
                },
                keyup: function() {
                    return self.onKeyUp.apply(self, arguments);
                },
                keypress: function() {
                    return self.onKeyPress.apply(self, arguments);
                },
                resize: function() {
                    self.positionDropdown.apply(self, []);
                },
                blur: function() {
                    return self.onBlur.apply(self, arguments);
                },
                focus: function() {
                    return self.onFocus.apply(self, arguments);
                }
            });
            $document.on("keydown" + eventNS, function(e) {
                self.isCmdDown = e[IS_MAC ? "metaKey" : "ctrlKey"];
                self.isCtrlDown = e[IS_MAC ? "altKey" : "ctrlKey"];
                self.isShiftDown = e.shiftKey;
            });
            $document.on("keyup" + eventNS, function(e) {
                if (e.keyCode === KEY_CTRL) self.isCtrlDown = false;
                if (e.keyCode === KEY_SHIFT) self.isShiftDown = false;
                if (e.keyCode === KEY_CMD) self.isCmdDown = false;
            });
            $document.on("mousedown" + eventNS, function(e) {
                if (self.isFocused) {
                    if (e.target === self.$dropdown[0] || e.target.parentNode === self.$dropdown[0]) {
                        return false;
                    }
                    if (!self.$control.has(e.target).length && e.target !== self.$control[0]) {
                        self.blur();
                    }
                }
            });
            $window.on([ "scroll" + eventNS, "resize" + eventNS ].join(" "), function() {
                if (self.isOpen) {
                    self.positionDropdown.apply(self, arguments);
                }
            });
            $window.on("mousemove" + eventNS, function() {
                self.ignoreHover = false;
            });
            this.revertSettings = {
                $children: self.$input.children().detach(),
                tabindex: self.$input.attr("tabindex")
            };
            self.$input.attr("tabindex", -1).hide().after(self.$wrapper);
            if ($.isArray(settings.items)) {
                self.setValue(settings.items);
                delete settings.items;
            }
            if (self.$input[0].validity) {
                self.$input.on("invalid" + eventNS, function(e) {
                    e.preventDefault();
                    self.isInvalid = true;
                    self.refreshState();
                });
            }
            self.updateOriginalInput();
            self.refreshItems();
            self.refreshState();
            self.updatePlaceholder();
            self.isSetup = true;
            if (self.$input.is(":disabled")) {
                self.disable();
            }
            self.on("change", this.onChange);
            self.trigger("initialize");
            if (settings.preload) {
                self.onSearchChange("");
            }
        },
        setupTemplates: function() {
            var self = this;
            var field_label = self.settings.labelField;
            var field_optgroup = self.settings.optgroupLabelField;
            var templates = {
                optgroup: function(data) {
                    return '<div class="optgroup">' + data.html + "</div>";
                },
                optgroup_header: function(data, escape) {
                    return '<div class="optgroup-header">' + escape(data[field_optgroup]) + "</div>";
                },
                option: function(data, escape) {
                    return '<div class="option">' + escape(data[field_label]) + "</div>";
                },
                item: function(data, escape) {
                    return '<div class="item">' + escape(data[field_label]) + "</div>";
                },
                option_create: function(data, escape) {
                    return '<div class="create">Add <strong>' + escape(data.input) + "</strong>&hellip;</div>";
                }
            };
            self.settings.render = $.extend({}, templates, self.settings.render);
        },
        setupCallbacks: function() {
            var key, fn, callbacks = {
                initialize: "onInitialize",
                change: "onChange",
                item_add: "onItemAdd",
                item_remove: "onItemRemove",
                clear: "onClear",
                option_add: "onOptionAdd",
                option_remove: "onOptionRemove",
                option_clear: "onOptionClear",
                dropdown_open: "onDropdownOpen",
                dropdown_close: "onDropdownClose",
                type: "onType"
            };
            for (key in callbacks) {
                if (callbacks.hasOwnProperty(key)) {
                    fn = this.settings[callbacks[key]];
                    if (fn) this.on(key, fn);
                }
            }
        },
        onClick: function(e) {
            var self = this;
            if (!self.isFocused) {
                self.focus();
                e.preventDefault();
            }
        },
        onMouseDown: function(e) {
            var self = this;
            var defaultPrevented = e.isDefaultPrevented();
            var $target = $(e.target);
            if (self.isFocused) {
                if (e.target !== self.$control_input[0]) {
                    if (self.settings.mode === "single") {
                        self.isOpen ? self.close() : self.open();
                    } else if (!defaultPrevented) {
                        self.setActiveItem(null);
                    }
                    return false;
                }
            } else {
                if (!defaultPrevented) {
                    window.setTimeout(function() {
                        self.focus();
                    }, 0);
                }
            }
        },
        onChange: function() {
            this.$input.trigger("change");
        },
        onKeyPress: function(e) {
            if (this.isLocked) return e && e.preventDefault();
            var character = String.fromCharCode(e.keyCode || e.which);
            if (this.settings.create && character === this.settings.delimiter) {
                this.createItem();
                e.preventDefault();
                return false;
            }
        },
        onKeyDown: function(e) {
            var isInput = e.target === this.$control_input[0];
            var self = this;
            if (self.isLocked) {
                if (e.keyCode !== KEY_TAB) {
                    e.preventDefault();
                }
                return;
            }
            switch (e.keyCode) {
              case KEY_A:
                if (self.isCmdDown) {
                    self.selectAll();
                    return;
                }
                break;

              case KEY_ESC:
                self.close();
                return;

              case KEY_DOWN:
                if (!self.isOpen && self.hasOptions) {
                    self.open();
                } else if (self.$activeOption) {
                    self.ignoreHover = true;
                    var $next = self.getAdjacentOption(self.$activeOption, 1);
                    if ($next.length) self.setActiveOption($next, true, true);
                }
                e.preventDefault();
                return;

              case KEY_UP:
                if (self.$activeOption) {
                    self.ignoreHover = true;
                    var $prev = self.getAdjacentOption(self.$activeOption, -1);
                    if ($prev.length) self.setActiveOption($prev, true, true);
                }
                e.preventDefault();
                return;

              case KEY_RETURN:
                if (self.isOpen && self.$activeOption) {
                    self.onOptionSelect({
                        currentTarget: self.$activeOption
                    });
                }
                e.preventDefault();
                return;

              case KEY_LEFT:
                self.advanceSelection(-1, e);
                return;

              case KEY_RIGHT:
                self.advanceSelection(1, e);
                return;

              case KEY_TAB:
                if (self.settings.create && self.createItem()) {
                    e.preventDefault();
                }
                return;

              case KEY_BACKSPACE:
              case KEY_DELETE:
                self.deleteSelection(e);
                return;
            }
            if (self.isFull() || self.isInputHidden) {
                e.preventDefault();
                return;
            }
        },
        onKeyUp: function(e) {
            var self = this;
            if (self.isLocked) return e && e.preventDefault();
            var value = self.$control_input.val() || "";
            if (self.lastValue !== value) {
                self.lastValue = value;
                self.onSearchChange(value);
                self.refreshOptions();
                self.trigger("type", value);
            }
        },
        onSearchChange: function(value) {
            var self = this;
            var fn = self.settings.load;
            if (!fn) return;
            if (self.loadedSearches.hasOwnProperty(value)) return;
            self.loadedSearches[value] = true;
            self.load(function(callback) {
                fn.apply(self, [ value, callback ]);
            });
        },
        onFocus: function(e) {
            var self = this;
            self.isFocused = true;
            if (self.isDisabled) {
                self.blur();
                e && e.preventDefault();
                return false;
            }
            if (self.ignoreFocus) return;
            if (self.settings.preload === "focus") self.onSearchChange("");
            if (!self.$activeItems.length) {
                self.showInput();
                self.setActiveItem(null);
                self.refreshOptions(!!self.settings.openOnFocus);
            }
            self.refreshState();
        },
        onBlur: function(e) {
            var self = this;
            self.isFocused = false;
            if (self.ignoreFocus) return;
            if (self.settings.create && self.settings.createOnBlur) {
                self.createItem();
            }
            self.close();
            self.setTextboxValue("");
            self.setActiveItem(null);
            self.setActiveOption(null);
            self.setCaret(self.items.length);
            self.refreshState();
        },
        onOptionHover: function(e) {
            if (this.ignoreHover) return;
            this.setActiveOption(e.currentTarget, false);
        },
        onOptionSelect: function(e) {
            var value, $target, $option, self = this;
            if (e.preventDefault) {
                e.preventDefault();
                e.stopPropagation();
            }
            $target = $(e.currentTarget);
            if ($target.hasClass("create")) {
                self.createItem();
            } else {
                value = $target.attr("data-value");
                if (value) {
                    self.lastQuery = null;
                    self.setTextboxValue("");
                    self.addItem(value);
                    if (!self.settings.hideSelected && e.type && /mouse/.test(e.type)) {
                        self.setActiveOption(self.getOption(value));
                    }
                }
            }
        },
        onItemSelect: function(e) {
            var self = this;
            if (self.isLocked) return;
            if (self.settings.mode === "multi") {
                e.preventDefault();
                self.setActiveItem(e.currentTarget, e);
            }
        },
        load: function(fn) {
            var self = this;
            var $wrapper = self.$wrapper.addClass("loading");
            self.loading++;
            fn.apply(self, [ function(results) {
                self.loading = Math.max(self.loading - 1, 0);
                if (results && results.length) {
                    self.addOption(results);
                    self.refreshOptions(self.isFocused && !self.isInputHidden);
                }
                if (!self.loading) {
                    $wrapper.removeClass("loading");
                }
                self.trigger("load", results);
            } ]);
        },
        setTextboxValue: function(value) {
            this.$control_input.val(value).triggerHandler("update");
            this.lastValue = value;
        },
        getValue: function() {
            if (this.tagType === TAG_SELECT && this.$input.attr("multiple")) {
                return this.items;
            } else {
                return this.items.join(this.settings.delimiter);
            }
        },
        setValue: function(value) {
            debounce_events(this, [ "change" ], function() {
                this.clear();
                var items = $.isArray(value) ? value : [ value ];
                for (var i = 0, n = items.length; i < n; i++) {
                    this.addItem(items[i]);
                }
            });
        },
        setActiveItem: function($item, e) {
            var self = this;
            var eventName;
            var i, idx, begin, end, item, swap;
            var $last;
            if (self.settings.mode === "single") return;
            $item = $($item);
            if (!$item.length) {
                $(self.$activeItems).removeClass("active");
                self.$activeItems = [];
                if (self.isFocused) {
                    self.showInput();
                }
                return;
            }
            eventName = e && e.type.toLowerCase();
            if (eventName === "mousedown" && self.isShiftDown && self.$activeItems.length) {
                $last = self.$control.children(".active:last");
                begin = Array.prototype.indexOf.apply(self.$control[0].childNodes, [ $last[0] ]);
                end = Array.prototype.indexOf.apply(self.$control[0].childNodes, [ $item[0] ]);
                if (begin > end) {
                    swap = begin;
                    begin = end;
                    end = swap;
                }
                for (i = begin; i <= end; i++) {
                    item = self.$control[0].childNodes[i];
                    if (self.$activeItems.indexOf(item) === -1) {
                        $(item).addClass("active");
                        self.$activeItems.push(item);
                    }
                }
                e.preventDefault();
            } else if (eventName === "mousedown" && self.isCtrlDown || eventName === "keydown" && this.isShiftDown) {
                if ($item.hasClass("active")) {
                    idx = self.$activeItems.indexOf($item[0]);
                    self.$activeItems.splice(idx, 1);
                    $item.removeClass("active");
                } else {
                    self.$activeItems.push($item.addClass("active")[0]);
                }
            } else {
                $(self.$activeItems).removeClass("active");
                self.$activeItems = [ $item.addClass("active")[0] ];
            }
            self.hideInput();
            if (!this.isFocused) {
                self.focus();
            }
        },
        setActiveOption: function($option, scroll, animate) {
            var height_menu, height_item, y;
            var scroll_top, scroll_bottom;
            var self = this;
            if (self.$activeOption) self.$activeOption.removeClass("active");
            self.$activeOption = null;
            $option = $($option);
            if (!$option.length) return;
            self.$activeOption = $option.addClass("active");
            if (scroll || !isset(scroll)) {
                height_menu = self.$dropdown_content.height();
                height_item = self.$activeOption.outerHeight(true);
                scroll = self.$dropdown_content.scrollTop() || 0;
                y = self.$activeOption.offset().top - self.$dropdown_content.offset().top + scroll;
                scroll_top = y;
                scroll_bottom = y - height_menu + height_item;
                if (y + height_item > height_menu + scroll) {
                    self.$dropdown_content.stop().animate({
                        scrollTop: scroll_bottom
                    }, animate ? self.settings.scrollDuration : 0);
                } else if (y < scroll) {
                    self.$dropdown_content.stop().animate({
                        scrollTop: scroll_top
                    }, animate ? self.settings.scrollDuration : 0);
                }
            }
        },
        selectAll: function() {
            var self = this;
            if (self.settings.mode === "single") return;
            self.$activeItems = Array.prototype.slice.apply(self.$control.children(":not(input)").addClass("active"));
            if (self.$activeItems.length) {
                self.hideInput();
                self.close();
            }
            self.focus();
        },
        hideInput: function() {
            var self = this;
            self.setTextboxValue("");
            self.$control_input.css({
                opacity: 0,
                position: "absolute",
                left: self.rtl ? 1e4 : -1e4
            });
            self.isInputHidden = true;
        },
        showInput: function() {
            this.$control_input.css({
                opacity: 1,
                position: "relative",
                left: 0
            });
            this.isInputHidden = false;
        },
        focus: function() {
            var self = this;
            if (self.isDisabled) return;
            self.ignoreFocus = true;
            self.$control_input[0].focus();
            window.setTimeout(function() {
                self.ignoreFocus = false;
                self.onFocus();
            }, 0);
        },
        blur: function() {
            this.$control_input.trigger("blur");
        },
        getScoreFunction: function(query) {
            return this.sifter.getScoreFunction(query, this.getSearchOptions());
        },
        getSearchOptions: function() {
            var settings = this.settings;
            var sort = settings.sortField;
            if (typeof sort === "string") {
                sort = {
                    field: sort
                };
            }
            return {
                fields: settings.searchField,
                conjunction: settings.searchConjunction,
                sort: sort
            };
        },
        search: function(query) {
            var i, value, score, result, calculateScore;
            var self = this;
            var settings = self.settings;
            var options = this.getSearchOptions();
            if (settings.score) {
                calculateScore = self.settings.score.apply(this, [ query ]);
                if (typeof calculateScore !== "function") {
                    throw new Error('Selectize "score" setting must be a function that returns a function');
                }
            }
            if (query !== self.lastQuery) {
                self.lastQuery = query;
                result = self.sifter.search(query, $.extend(options, {
                    score: calculateScore
                }));
                self.currentResults = result;
            } else {
                result = $.extend(true, {}, self.currentResults);
            }
            if (settings.hideSelected) {
                for (i = result.items.length - 1; i >= 0; i--) {
                    if (self.items.indexOf(hash_key(result.items[i].id)) !== -1) {
                        result.items.splice(i, 1);
                    }
                }
            }
            return result;
        },
        refreshOptions: function(triggerDropdown) {
            var i, j, k, n, groups, groups_order, option, option_html, optgroup, optgroups, html, html_children, has_create_option;
            var $active, $active_before, $create;
            if (typeof triggerDropdown === "undefined") {
                triggerDropdown = true;
            }
            var self = this;
            var query = self.$control_input.val();
            var results = self.search(query);
            var $dropdown_content = self.$dropdown_content;
            var active_before = self.$activeOption && hash_key(self.$activeOption.attr("data-value"));
            n = results.items.length;
            if (typeof self.settings.maxOptions === "number") {
                n = Math.min(n, self.settings.maxOptions);
            }
            groups = {};
            if (self.settings.optgroupOrder) {
                groups_order = self.settings.optgroupOrder;
                for (i = 0; i < groups_order.length; i++) {
                    groups[groups_order[i]] = [];
                }
            } else {
                groups_order = [];
            }
            for (i = 0; i < n; i++) {
                option = self.options[results.items[i].id];
                option_html = self.render("option", option);
                optgroup = option[self.settings.optgroupField] || "";
                optgroups = $.isArray(optgroup) ? optgroup : [ optgroup ];
                for (j = 0, k = optgroups && optgroups.length; j < k; j++) {
                    optgroup = optgroups[j];
                    if (!self.optgroups.hasOwnProperty(optgroup)) {
                        optgroup = "";
                    }
                    if (!groups.hasOwnProperty(optgroup)) {
                        groups[optgroup] = [];
                        groups_order.push(optgroup);
                    }
                    groups[optgroup].push(option_html);
                }
            }
            html = [];
            for (i = 0, n = groups_order.length; i < n; i++) {
                optgroup = groups_order[i];
                if (self.optgroups.hasOwnProperty(optgroup) && groups[optgroup].length) {
                    html_children = self.render("optgroup_header", self.optgroups[optgroup]) || "";
                    html_children += groups[optgroup].join("");
                    html.push(self.render("optgroup", $.extend({}, self.optgroups[optgroup], {
                        html: html_children
                    })));
                } else {
                    html.push(groups[optgroup].join(""));
                }
            }
            $dropdown_content.html(html.join(""));
            if (self.settings.highlight && results.query.length && results.tokens.length) {
                for (i = 0, n = results.tokens.length; i < n; i++) {
                    highlight($dropdown_content, results.tokens[i].regex);
                }
            }
            if (!self.settings.hideSelected) {
                for (i = 0, n = self.items.length; i < n; i++) {
                    self.getOption(self.items[i]).addClass("selected");
                }
            }
            has_create_option = self.settings.create && results.query.length;
            if (has_create_option) {
                $dropdown_content.prepend(self.render("option_create", {
                    input: query
                }));
                $create = $($dropdown_content[0].childNodes[0]);
            }
            self.hasOptions = results.items.length > 0 || has_create_option;
            if (self.hasOptions) {
                if (results.items.length > 0) {
                    $active_before = active_before && self.getOption(active_before);
                    if ($active_before && $active_before.length) {
                        $active = $active_before;
                    } else if (self.settings.mode === "single" && self.items.length) {
                        $active = self.getOption(self.items[0]);
                    }
                    if (!$active || !$active.length) {
                        if ($create && !self.settings.addPrecedence) {
                            $active = self.getAdjacentOption($create, 1);
                        } else {
                            $active = $dropdown_content.find("[data-selectable]:first");
                        }
                    }
                } else {
                    $active = $create;
                }
                self.setActiveOption($active);
                if (triggerDropdown && !self.isOpen) {
                    self.open();
                }
            } else {
                self.setActiveOption(null);
                if (triggerDropdown && self.isOpen) {
                    self.close();
                }
            }
        },
        addOption: function(data) {
            var i, n, optgroup, value, self = this;
            if ($.isArray(data)) {
                for (i = 0, n = data.length; i < n; i++) {
                    self.addOption(data[i]);
                }
                return;
            }
            value = hash_key(data[self.settings.valueField]);
            if (!value || self.options.hasOwnProperty(value)) return;
            self.userOptions[value] = true;
            self.options[value] = data;
            self.lastQuery = null;
            self.trigger("option_add", value, data);
        },
        addOptionGroup: function(id, data) {
            this.optgroups[id] = data;
            this.trigger("optgroup_add", id, data);
        },
        updateOption: function(value, data) {
            var self = this;
            var $item, $item_new;
            var value_new, index_item, cache_items, cache_options;
            value = hash_key(value);
            value_new = hash_key(data[self.settings.valueField]);
            if (!self.options.hasOwnProperty(value)) return;
            if (!value_new) throw new Error("Value must be set in option data");
            if (value_new !== value) {
                delete self.options[value];
                index_item = self.items.indexOf(value);
                if (index_item !== -1) {
                    self.items.splice(index_item, 1, value_new);
                }
            }
            self.options[value_new] = data;
            cache_items = self.renderCache["item"];
            cache_options = self.renderCache["option"];
            if (isset(cache_items)) {
                delete cache_items[value];
                delete cache_items[value_new];
            }
            if (isset(cache_options)) {
                delete cache_options[value];
                delete cache_options[value_new];
            }
            if (self.items.indexOf(value_new) !== -1) {
                $item = self.getItem(value);
                $item_new = $(self.render("item", data));
                if ($item.hasClass("active")) $item_new.addClass("active");
                $item.replaceWith($item_new);
            }
            if (self.isOpen) {
                self.refreshOptions(false);
            }
        },
        removeOption: function(value) {
            var self = this;
            value = hash_key(value);
            delete self.userOptions[value];
            delete self.options[value];
            self.lastQuery = null;
            self.trigger("option_remove", value);
            self.removeItem(value);
        },
        clearOptions: function() {
            var self = this;
            self.loadedSearches = {};
            self.userOptions = {};
            self.options = self.sifter.items = {};
            self.lastQuery = null;
            self.trigger("option_clear");
            self.clear();
        },
        getOption: function(value) {
            return this.getElementWithValue(value, this.$dropdown_content.find("[data-selectable]"));
        },
        getAdjacentOption: function($option, direction) {
            var $options = this.$dropdown.find("[data-selectable]");
            var index = $options.index($option) + direction;
            return index >= 0 && index < $options.length ? $options.eq(index) : $();
        },
        getElementWithValue: function(value, $els) {
            value = hash_key(value);
            if (value) {
                for (var i = 0, n = $els.length; i < n; i++) {
                    if ($els[i].getAttribute("data-value") === value) {
                        return $($els[i]);
                    }
                }
            }
            return $();
        },
        getItem: function(value) {
            return this.getElementWithValue(value, this.$control.children());
        },
        addItem: function(value) {
            debounce_events(this, [ "change" ], function() {
                var $item, $option;
                var self = this;
                var inputMode = self.settings.mode;
                var i, active, options, value_next;
                value = hash_key(value);
                if (self.items.indexOf(value) !== -1) {
                    if (inputMode === "single") self.close();
                    return;
                }
                if (!self.options.hasOwnProperty(value)) return;
                if (inputMode === "single") self.clear();
                if (inputMode === "multi" && self.isFull()) return;
                $item = $(self.render("item", self.options[value]));
                self.items.splice(self.caretPos, 0, value);
                self.insertAtCaret($item);
                self.refreshState();
                if (self.isSetup) {
                    options = self.$dropdown_content.find("[data-selectable]");
                    $option = self.getOption(value);
                    value_next = self.getAdjacentOption($option, 1).attr("data-value");
                    self.refreshOptions(self.isFocused && inputMode !== "single");
                    if (value_next) {
                        self.setActiveOption(self.getOption(value_next));
                    }
                    if (!options.length || self.settings.maxItems !== null && self.items.length >= self.settings.maxItems) {
                        self.close();
                    } else {
                        self.positionDropdown();
                    }
                    self.updatePlaceholder();
                    self.trigger("item_add", value, $item);
                    self.updateOriginalInput();
                }
            });
        },
        removeItem: function(value) {
            var self = this;
            var $item, i, idx;
            $item = typeof value === "object" ? value : self.getItem(value);
            value = hash_key($item.attr("data-value"));
            i = self.items.indexOf(value);
            if (i !== -1) {
                $item.remove();
                if ($item.hasClass("active")) {
                    idx = self.$activeItems.indexOf($item[0]);
                    self.$activeItems.splice(idx, 1);
                }
                self.items.splice(i, 1);
                self.lastQuery = null;
                if (!self.settings.persist && self.userOptions.hasOwnProperty(value)) {
                    self.removeOption(value);
                }
                if (i < self.caretPos) {
                    self.setCaret(self.caretPos - 1);
                }
                self.refreshState();
                self.updatePlaceholder();
                self.updateOriginalInput();
                self.positionDropdown();
                self.trigger("item_remove", value);
            }
        },
        createItem: function() {
            var self = this;
            var input = $.trim(self.$control_input.val() || "");
            var caret = self.caretPos;
            if (!input.length) return false;
            self.lock();
            var setup = typeof self.settings.create === "function" ? this.settings.create : function(input) {
                var data = {};
                data[self.settings.labelField] = input;
                data[self.settings.valueField] = input;
                return data;
            };
            var create = once(function(data) {
                self.unlock();
                if (!data || typeof data !== "object") return;
                var value = hash_key(data[self.settings.valueField]);
                if (!value) return;
                self.setTextboxValue("");
                self.addOption(data);
                self.setCaret(caret);
                self.addItem(value);
                self.refreshOptions(self.settings.mode !== "single");
            });
            var output = setup.apply(this, [ input, create ]);
            if (typeof output !== "undefined") {
                create(output);
            }
            return true;
        },
        refreshItems: function() {
            this.lastQuery = null;
            if (this.isSetup) {
                for (var i = 0; i < this.items.length; i++) {
                    this.addItem(this.items);
                }
            }
            this.refreshState();
            this.updateOriginalInput();
        },
        refreshState: function() {
            var self = this;
            var invalid = self.isRequired && !self.items.length;
            if (!invalid) self.isInvalid = false;
            self.$control_input.prop("required", invalid);
            self.refreshClasses();
        },
        refreshClasses: function() {
            var self = this;
            var isFull = self.isFull();
            var isLocked = self.isLocked;
            self.$wrapper.toggleClass("rtl", self.rtl);
            self.$control.toggleClass("focus", self.isFocused).toggleClass("disabled", self.isDisabled).toggleClass("required", self.isRequired).toggleClass("invalid", self.isInvalid).toggleClass("locked", isLocked).toggleClass("full", isFull).toggleClass("not-full", !isFull).toggleClass("input-active", self.isFocused && !self.isInputHidden).toggleClass("dropdown-active", self.isOpen).toggleClass("has-options", !$.isEmptyObject(self.options)).toggleClass("has-items", self.items.length > 0);
            self.$control_input.data("grow", !isFull && !isLocked);
        },
        isFull: function() {
            return this.settings.maxItems !== null && this.items.length >= this.settings.maxItems;
        },
        updateOriginalInput: function() {
            var i, n, options, self = this;
            if (self.$input[0].tagName.toLowerCase() === "select") {
                options = [];
                for (i = 0, n = self.items.length; i < n; i++) {
                    options.push('<option value="' + escape_html(self.items[i]) + '" selected="selected"></option>');
                }
                if (!options.length && !this.$input.attr("multiple")) {
                    options.push('<option value="" selected="selected"></option>');
                }
                self.$input.html(options.join(""));
            } else {
                self.$input.val(self.getValue());
            }
            if (self.isSetup) {
                self.trigger("change", self.$input.val());
            }
        },
        updatePlaceholder: function() {
            if (!this.settings.placeholder) return;
            var $input = this.$control_input;
            if (this.items.length) {
                $input.removeAttr("placeholder");
            } else {
                $input.attr("placeholder", this.settings.placeholder);
            }
            $input.triggerHandler("update");
        },
        open: function() {
            var self = this;
            if (self.isLocked || self.isOpen || self.settings.mode === "multi" && self.isFull()) return;
            self.focus();
            self.isOpen = true;
            self.refreshState();
            self.$dropdown.css({
                visibility: "hidden",
                display: "block"
            });
            self.positionDropdown();
            self.$dropdown.css({
                visibility: "visible"
            });
            self.trigger("dropdown_open", self.$dropdown);
        },
        close: function() {
            var self = this;
            var trigger = self.isOpen;
            if (self.settings.mode === "single" && self.items.length) {
                self.hideInput();
            }
            self.isOpen = false;
            self.$dropdown.hide();
            self.setActiveOption(null);
            self.refreshState();
            if (trigger) self.trigger("dropdown_close", self.$dropdown);
        },
        positionDropdown: function() {
            var $control = this.$control;
            var offset = this.settings.dropdownParent === "body" ? $control.offset() : $control.position();
            offset.top += $control.outerHeight(true);
            this.$dropdown.css({
                width: $control.outerWidth(),
                top: offset.top,
                left: offset.left
            });
        },
        clear: function() {
            var self = this;
            if (!self.items.length) return;
            self.$control.children(":not(input)").remove();
            self.items = [];
            self.setCaret(0);
            self.updatePlaceholder();
            self.updateOriginalInput();
            self.refreshState();
            self.showInput();
            self.trigger("clear");
        },
        insertAtCaret: function($el) {
            var caret = Math.min(this.caretPos, this.items.length);
            if (caret === 0) {
                this.$control.prepend($el);
            } else {
                $(this.$control[0].childNodes[caret]).before($el);
            }
            this.setCaret(caret + 1);
        },
        deleteSelection: function(e) {
            var i, n, direction, selection, values, caret, option_select, $option_select, $tail;
            var self = this;
            direction = e && e.keyCode === KEY_BACKSPACE ? -1 : 1;
            selection = getSelection(self.$control_input[0]);
            if (self.$activeOption && !self.settings.hideSelected) {
                option_select = self.getAdjacentOption(self.$activeOption, -1).attr("data-value");
            }
            values = [];
            if (self.$activeItems.length) {
                $tail = self.$control.children(".active:" + (direction > 0 ? "last" : "first"));
                caret = self.$control.children(":not(input)").index($tail);
                if (direction > 0) {
                    caret++;
                }
                for (i = 0, n = self.$activeItems.length; i < n; i++) {
                    values.push($(self.$activeItems[i]).attr("data-value"));
                }
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            } else if ((self.isFocused || self.settings.mode === "single") && self.items.length) {
                if (direction < 0 && selection.start === 0 && selection.length === 0) {
                    values.push(self.items[self.caretPos - 1]);
                } else if (direction > 0 && selection.start === self.$control_input.val().length) {
                    values.push(self.items[self.caretPos]);
                }
            }
            if (!values.length || typeof self.settings.onDelete === "function" && self.settings.onDelete.apply(self, [ values ]) === false) {
                return false;
            }
            if (typeof caret !== "undefined") {
                self.setCaret(caret);
            }
            while (values.length) {
                self.removeItem(values.pop());
            }
            self.showInput();
            self.positionDropdown();
            self.refreshOptions(true);
            if (option_select) {
                $option_select = self.getOption(option_select);
                if ($option_select.length) {
                    self.setActiveOption($option_select);
                }
            }
            return true;
        },
        advanceSelection: function(direction, e) {
            var tail, selection, idx, valueLength, cursorAtEdge, $tail;
            var self = this;
            if (direction === 0) return;
            if (self.rtl) direction *= -1;
            tail = direction > 0 ? "last" : "first";
            selection = getSelection(self.$control_input[0]);
            if (self.isFocused && !self.isInputHidden) {
                valueLength = self.$control_input.val().length;
                cursorAtEdge = direction < 0 ? selection.start === 0 && selection.length === 0 : selection.start === valueLength;
                if (cursorAtEdge && !valueLength) {
                    self.advanceCaret(direction, e);
                }
            } else {
                $tail = self.$control.children(".active:" + tail);
                if ($tail.length) {
                    idx = self.$control.children(":not(input)").index($tail);
                    self.setActiveItem(null);
                    self.setCaret(direction > 0 ? idx + 1 : idx);
                }
            }
        },
        advanceCaret: function(direction, e) {
            var self = this, fn, $adj;
            if (direction === 0) return;
            fn = direction > 0 ? "next" : "prev";
            if (self.isShiftDown) {
                $adj = self.$control_input[fn]();
                if ($adj.length) {
                    self.hideInput();
                    self.setActiveItem($adj);
                    e && e.preventDefault();
                }
            } else {
                self.setCaret(self.caretPos + direction);
            }
        },
        setCaret: function(i) {
            var self = this;
            if (self.settings.mode === "single") {
                i = self.items.length;
            } else {
                i = Math.max(0, Math.min(self.items.length, i));
            }
            var j, n, fn, $children, $child;
            $children = self.$control.children(":not(input)");
            for (j = 0, n = $children.length; j < n; j++) {
                $child = $($children[j]).detach();
                if (j < i) {
                    self.$control_input.before($child);
                } else {
                    self.$control.append($child);
                }
            }
            self.caretPos = i;
        },
        lock: function() {
            this.close();
            this.isLocked = true;
            this.refreshState();
        },
        unlock: function() {
            this.isLocked = false;
            this.refreshState();
        },
        disable: function() {
            var self = this;
            self.$input.prop("disabled", true);
            self.isDisabled = true;
            self.lock();
        },
        enable: function() {
            var self = this;
            self.$input.prop("disabled", false);
            self.isDisabled = false;
            self.unlock();
        },
        destroy: function() {
            var self = this;
            var eventNS = self.eventNS;
            var revertSettings = self.revertSettings;
            self.trigger("destroy");
            self.off();
            self.$wrapper.remove();
            self.$dropdown.remove();
            self.$input.html("").append(revertSettings.$children).removeAttr("tabindex").attr({
                tabindex: revertSettings.tabindex
            }).show();
            $(window).off(eventNS);
            $(document).off(eventNS);
            $(document.body).off(eventNS);
            delete self.$input[0].selectize;
        },
        render: function(templateName, data) {
            var value, id, label;
            var html = "";
            var cache = false;
            var self = this;
            var regex_tag = /^[\t ]*<([a-z][a-z0-9\-_]*(?:\:[a-z][a-z0-9\-_]*)?)/i;
            if (templateName === "option" || templateName === "item") {
                value = hash_key(data[self.settings.valueField]);
                cache = !!value;
            }
            if (cache) {
                if (!isset(self.renderCache[templateName])) {
                    self.renderCache[templateName] = {};
                }
                if (self.renderCache[templateName].hasOwnProperty(value)) {
                    return self.renderCache[templateName][value];
                }
            }
            html = self.settings.render[templateName].apply(this, [ data, escape_html ]);
            if (templateName === "option" || templateName === "option_create") {
                html = html.replace(regex_tag, "<$1 data-selectable");
            }
            if (templateName === "optgroup") {
                id = data[self.settings.optgroupValueField] || "";
                html = html.replace(regex_tag, '<$1 data-group="' + escape_replace(escape_html(id)) + '"');
            }
            if (templateName === "option" || templateName === "item") {
                html = html.replace(regex_tag, '<$1 data-value="' + escape_replace(escape_html(value || "")) + '"');
            }
            if (cache) {
                self.renderCache[templateName][value] = html;
            }
            return html;
        }
    });
    Selectize.count = 0;
    Selectize.defaults = {
        plugins: [],
        delimiter: ",",
        persist: true,
        diacritics: true,
        create: false,
        createOnBlur: false,
        highlight: true,
        openOnFocus: true,
        maxOptions: 1e3,
        maxItems: null,
        hideSelected: null,
        addPrecedence: false,
        preload: false,
        scrollDuration: 60,
        loadThrottle: 300,
        dataAttr: "data-data",
        optgroupField: "optgroup",
        valueField: "value",
        labelField: "text",
        optgroupLabelField: "label",
        optgroupValueField: "value",
        optgroupOrder: null,
        sortField: "$order",
        searchField: [ "text" ],
        searchConjunction: "and",
        mode: null,
        wrapperClass: "selectize-control",
        inputClass: "selectize-input",
        dropdownClass: "selectize-dropdown",
        dropdownContentClass: "selectize-dropdown-content",
        dropdownParent: null,
        render: {}
    };
    $.fn.selectize = function(settings_user) {
        var defaults = $.fn.selectize.defaults;
        var settings = $.extend({}, defaults, settings_user);
        var attr_data = settings.dataAttr;
        var field_label = settings.labelField;
        var field_value = settings.valueField;
        var field_optgroup = settings.optgroupField;
        var field_optgroup_label = settings.optgroupLabelField;
        var field_optgroup_value = settings.optgroupValueField;
        var init_textbox = function($input, settings_element) {
            var i, n, values, option, value = $.trim($input.val() || "");
            if (!value.length) return;
            values = value.split(settings.delimiter);
            for (i = 0, n = values.length; i < n; i++) {
                option = {};
                option[field_label] = values[i];
                option[field_value] = values[i];
                settings_element.options[values[i]] = option;
            }
            settings_element.items = values;
        };
        var init_select = function($input, settings_element) {
            var i, n, tagName, $children, order = 0;
            var options = settings_element.options;
            var readData = function($el) {
                var data = attr_data && $el.attr(attr_data);
                if (typeof data === "string" && data.length) {
                    return JSON.parse(data);
                }
                return null;
            };
            var addOption = function($option, group) {
                var value, option;
                $option = $($option);
                value = $option.attr("value") || "";
                if (!value.length) return;
                if (options.hasOwnProperty(value)) {
                    if (group) {
                        if (!options[value].optgroup) {
                            options[value].optgroup = group;
                        } else if (!$.isArray(options[value].optgroup)) {
                            options[value].optgroup = [ options[value].optgroup, group ];
                        } else {
                            options[value].optgroup.push(group);
                        }
                    }
                    return;
                }
                option = readData($option) || {};
                option[field_label] = option[field_label] || $option.text();
                option[field_value] = option[field_value] || value;
                option[field_optgroup] = option[field_optgroup] || group;
                option.$order = ++order;
                options[value] = option;
                if ($option.is(":selected")) {
                    settings_element.items.push(value);
                }
            };
            var addGroup = function($optgroup) {
                var i, n, id, optgroup, $options;
                $optgroup = $($optgroup);
                id = $optgroup.attr("label");
                if (id) {
                    optgroup = readData($optgroup) || {};
                    optgroup[field_optgroup_label] = id;
                    optgroup[field_optgroup_value] = id;
                    settings_element.optgroups[id] = optgroup;
                }
                $options = $("option", $optgroup);
                for (i = 0, n = $options.length; i < n; i++) {
                    addOption($options[i], id);
                }
            };
            settings_element.maxItems = $input.attr("multiple") ? null : 1;
            $children = $input.children();
            for (i = 0, n = $children.length; i < n; i++) {
                tagName = $children[i].tagName.toLowerCase();
                if (tagName === "optgroup") {
                    addGroup($children[i]);
                } else if (tagName === "option") {
                    addOption($children[i]);
                }
            }
        };
        return this.each(function() {
            if (this.selectize) return;
            var instance;
            var $input = $(this);
            var tag_name = this.tagName.toLowerCase();
            var settings_element = {
                placeholder: $input.children('option[value=""]').text() || $input.attr("placeholder"),
                options: {},
                optgroups: {},
                items: []
            };
            if (tag_name === "select") {
                init_select($input, settings_element);
            } else {
                init_textbox($input, settings_element);
            }
            instance = new Selectize($input, $.extend(true, {}, defaults, settings_element, settings_user));
            $input.data("selectize", instance);
            $input.addClass("selectized");
        });
    };
    $.fn.selectize.defaults = Selectize.defaults;
    Selectize.define("drag_drop", function(options) {
        if (!$.fn.sortable) throw new Error('The "drag_drop" plugin requires jQuery UI "sortable".');
        if (this.settings.mode !== "multi") return;
        var self = this;
        self.lock = function() {
            var original = self.lock;
            return function() {
                var sortable = self.$control.data("sortable");
                if (sortable) sortable.disable();
                return original.apply(self, arguments);
            };
        }();
        self.unlock = function() {
            var original = self.unlock;
            return function() {
                var sortable = self.$control.data("sortable");
                if (sortable) sortable.enable();
                return original.apply(self, arguments);
            };
        }();
        self.setup = function() {
            var original = self.setup;
            return function() {
                original.apply(this, arguments);
                var $control = self.$control.sortable({
                    items: "[data-value]",
                    forcePlaceholderSize: true,
                    disabled: self.isLocked,
                    start: function(e, ui) {
                        ui.placeholder.css("width", ui.helper.css("width"));
                        $control.css({
                            overflow: "visible"
                        });
                    },
                    stop: function() {
                        $control.css({
                            overflow: "hidden"
                        });
                        var active = self.$activeItems ? self.$activeItems.slice() : null;
                        var values = [];
                        $control.children("[data-value]").each(function() {
                            values.push($(this).attr("data-value"));
                        });
                        self.setValue(values);
                        self.setActiveItem(active);
                    }
                });
            };
        }();
    });
    Selectize.define("dropdown_header", function(options) {
        var self = this;
        options = $.extend({
            title: "Untitled",
            headerClass: "selectize-dropdown-header",
            titleRowClass: "selectize-dropdown-header-title",
            labelClass: "selectize-dropdown-header-label",
            closeClass: "selectize-dropdown-header-close",
            html: function(data) {
                return '<div class="' + data.headerClass + '">' + '<div class="' + data.titleRowClass + '">' + '<span class="' + data.labelClass + '">' + data.title + "</span>" + '<a href="javascript:void(0)" class="' + data.closeClass + '">&times;</a>' + "</div>" + "</div>";
            }
        }, options);
        self.setup = function() {
            var original = self.setup;
            return function() {
                original.apply(self, arguments);
                self.$dropdown_header = $(options.html(options));
                self.$dropdown.prepend(self.$dropdown_header);
            };
        }();
    });
    Selectize.define("optgroup_columns", function(options) {
        var self = this;
        options = $.extend({
            equalizeWidth: true,
            equalizeHeight: true
        }, options);
        this.getAdjacentOption = function($option, direction) {
            var $options = $option.closest("[data-group]").find("[data-selectable]");
            var index = $options.index($option) + direction;
            return index >= 0 && index < $options.length ? $options.eq(index) : $();
        };
        this.onKeyDown = function() {
            var original = self.onKeyDown;
            return function(e) {
                var index, $option, $options, $optgroup;
                if (this.isOpen && (e.keyCode === KEY_LEFT || e.keyCode === KEY_RIGHT)) {
                    self.ignoreHover = true;
                    $optgroup = this.$activeOption.closest("[data-group]");
                    index = $optgroup.find("[data-selectable]").index(this.$activeOption);
                    if (e.keyCode === KEY_LEFT) {
                        $optgroup = $optgroup.prev("[data-group]");
                    } else {
                        $optgroup = $optgroup.next("[data-group]");
                    }
                    $options = $optgroup.find("[data-selectable]");
                    $option = $options.eq(Math.min($options.length - 1, index));
                    if ($option.length) {
                        this.setActiveOption($option);
                    }
                    return;
                }
                return original.apply(this, arguments);
            };
        }();
        var equalizeSizes = function() {
            var i, n, height_max, width, width_last, width_parent, $optgroups;
            $optgroups = $("[data-group]", self.$dropdown_content);
            n = $optgroups.length;
            if (!n || !self.$dropdown_content.width()) return;
            if (options.equalizeHeight) {
                height_max = 0;
                for (i = 0; i < n; i++) {
                    height_max = Math.max(height_max, $optgroups.eq(i).height());
                }
                $optgroups.css({
                    height: height_max
                });
            }
            if (options.equalizeWidth) {
                width_parent = self.$dropdown_content.innerWidth();
                width = Math.round(width_parent / n);
                $optgroups.css({
                    width: width
                });
                if (n > 1) {
                    width_last = width_parent - width * (n - 1);
                    $optgroups.eq(n - 1).css({
                        width: width_last
                    });
                }
            }
        };
        if (options.equalizeHeight || options.equalizeWidth) {
            hook.after(this, "positionDropdown", equalizeSizes);
            hook.after(this, "refreshOptions", equalizeSizes);
        }
    });
    Selectize.define("remove_button", function(options) {
        if (this.settings.mode === "single") return;
        options = $.extend({
            label: "&times;",
            title: "Remove",
            className: "remove",
            append: true
        }, options);
        var self = this;
        var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + escape_html(options.title) + '">' + options.label + "</a>";
        var append = function(html_container, html_element) {
            var pos = html_container.search(/(<\/[^>]+>\s*)$/);
            return html_container.substring(0, pos) + html_element + html_container.substring(pos);
        };
        this.setup = function() {
            var original = self.setup;
            return function() {
                if (options.append) {
                    var render_item = self.settings.render.item;
                    self.settings.render.item = function(data) {
                        return append(render_item.apply(this, arguments), html);
                    };
                }
                original.apply(this, arguments);
                this.$control.on("click", "." + options.className, function(e) {
                    e.preventDefault();
                    if (self.isLocked) return;
                    var $item = $(e.target).parent();
                    self.setActiveItem($item);
                    if (self.deleteSelection()) {
                        self.setCaret(self.items.length);
                    }
                });
            };
        }();
    });
    Selectize.define("restore_on_backspace", function(options) {
        var self = this;
        options.text = options.text || function(option) {
            return option[this.settings.labelField];
        };
        this.onKeyDown = function(e) {
            var original = self.onKeyDown;
            return function(e) {
                var index, option;
                if (e.keyCode === KEY_BACKSPACE && this.$control_input.val() === "" && !this.$activeItems.length) {
                    index = this.caretPos - 1;
                    if (index >= 0 && index < this.items.length) {
                        option = this.options[this.items[index]];
                        if (this.deleteSelection(e)) {
                            this.setTextboxValue(options.text.apply(this, [ option ]));
                            this.refreshOptions(true);
                        }
                        e.preventDefault();
                        return;
                    }
                }
                return original.apply(this, arguments);
            };
        }();
    });
    return Selectize;
});

(function($) {
    "use strict";
    Selectize.define("remove_button", function(options) {
        if (this.settings.mode === "single") return;
        options = $.extend({
            label: "&times;",
            title: "Remove",
            className: "remove",
            append: true
        }, options);
        var self = this;
        var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + options.title + '">' + options.label + "</a>";
        var append = function(html_container, html_element) {
            var pos = html_container.search(/(<\/[^>]+>\s*)$/);
            return html_container.substring(0, pos) + html_element + html_container.substring(pos);
        };
        this.setup = function() {
            var original = self.setup;
            return function() {
                if (options.append) {
                    var render_item = self.settings.render.item;
                    self.settings.render.item = function(data) {
                        return append(render_item.apply(this, arguments), html);
                    };
                }
                original.apply(this, arguments);
                this.$control.on("click", "." + options.className, function(e) {
                    e.preventDefault();
                    if (self.isLocked) return;
                    var $item = $(e.currentTarget).parent();
                    self.setActiveItem($item);
                    if (self.deleteSelection()) {
                        self.setCaret(self.items.length);
                    }
                });
            };
        }();
    });
})(jQuery);
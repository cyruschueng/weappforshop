var curMenuIndex = getParam("nav");
if (curMenuIndex && /^\d{1,2}$/.test(curMenuIndex)) {
    $("#ulLeftNav").find("a").removeClass("active").eq(curMenuIndex - 1).addClass("active");
}

function getParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null)
        return unescape(r[2]);
    return null; //返回参数值
}
;

/*
 * select 插件
 */
$.fn.select = function (changeCallback) {
    var selects = [];
    if (this.length) {
        $.each(this, function (selectIndex) {
            var selectedIndex = 0, tempSelectedIndex = -1, me = {
                getSelectedValue: function () {
                    var selectedItem = selectItems.eq(selectedIndex), selectedVal = selectedItem.attr("data-value");
                    return selectedVal == null || selectedVal == undefined ? selectedItem.text() : selectedVal;
                },
                getSelectedIndex: function () {
                    return selectedIndex;
                },
                setSelectedIndex: function (index) {
                    if (index > -1 && index < selectItems.length) {
                        selectedIndex = index;

                        handleChange(selectItems.eq(index), me, tempSelectedIndex, index, "code");

                        tempSelectedIndex = index;
                    }

                    return me;
                },
                setSelectedValue: function (val) {
                    if (val !== undefined) {
                        $.each(selectItems, function (i) {
                            var currentItemVal = $(this).attr("data-value");
                            if (currentItemVal == undefined)
                                currentItemVal = $(this).text();

                            if (val == currentItemVal)
                                me.setSelectedIndex(i);
                        });
                    }
                },
                getSelectedText: function () {
                    return selectItems.eq(selectedIndex).text();
                },
                disable: function (isDisable) {
                    isDisable = isDisable === undefined ? true : isDisable;
                    me.dom[isDisable ? "addClass" : "removeClass"]("disable");
                    return me;
                },
                dom: $(this),
                index: selectIndex,
                init: function () {
                    selectItems = init();
                }
            };

            me.dom.prepend("<div></div>");

            selects.push(me);

            me.dom.click(function (e) {
                if (!$(this).hasClass("disable"))
                    $(this).toggleClass("active");
            });

            var selectItems = init();

            function init() {
                tempSelectedIndex = -1;
                selectedIndex = 0;
                me.dom.children("div").text("");
                var items = me.dom.find("li").each(function (i) {
                    $(this).click(function (e) {
                        selectedIndex = i;

                        handleChange($(this), me, tempSelectedIndex, i, "user");

                        tempSelectedIndex = i;

                        e.stopPropagation();
                    });

                    if ($(this).hasClass("selected"))
                        selectedIndex = i;
                });

                handleChange(items.eq(selectedIndex), me, tempSelectedIndex, selectedIndex, "init");

                tempSelectedIndex = selectedIndex;

                return items;
            }
        });
    } else {
        selects.push(null);
    }

    function handleChange(selectedDom, me, lastSelectedIndex, index, eventType) {
        if (lastSelectedIndex !== index && $.isFunction(changeCallback))
            changeCallback.call(me, index, eventType);

        selectedDom.addClass("selected").siblings().removeClass("selected");
        selectedDom.parent().prev().text(selectedDom.text());
    }

    return selects;
};

/*
 * tab插件
 */
$.fn.tabContent = function (changeCallback) {
    var tabs = [];
    if (this.length) {
        $.each(this, function () {
            var selectedIndex = 0, tempSelectedIndex = -1, me = {
                setSelectedIndex: function (index) {
                    selectedIndex = index;

                    handleChange(me, tempSelectedIndex, index, tabItems.eq(index), "code");

                    tempSelectedIndex = index;
                },
                getSelectedIndex: function () {
                    return selectedIndex;
                }
            };

            tabs.push(me);

            var tabItems = $(this).find("div.ipm-content-head li").each(function (i) {
                $(this).click(function () {
                    if ($(this).hasClass("disable"))
                        return;

                    selectedIndex = i;

                    handleChange(me, tempSelectedIndex, i, $(this), "user");

                    tempSelectedIndex = i;
                });

                if ($(this).hasClass("active"))
                    selectedIndex = i;
            });

            handleChange(me, tempSelectedIndex, selectedIndex, tabItems.eq(selectedIndex), "init");

            tempSelectedIndex = selectedIndex;
        });
    }

    function handleChange(me, lastSelectedIndex, index, activeTabDom, eventType) {
        if (lastSelectedIndex !== index && $.isFunction(changeCallback))
            changeCallback.call(me, index, eventType);

        activeTabDom.addClass("active").siblings().removeClass("active");

        $("#" + activeTabDom.attr("data-tab")).show().siblings().hide();
    }

    return tabs;
};

/* 表单验证插件 */
$.fn.getForm = function () {
    var result = {result: true, data: {}};
    if (this.length) {
        this.find("*[data-form]").each(function () {
            if (tools.isHiddle(this)) {
                return;
            }

            var type = $(this).attr("data-type"), ver = $(this).attr("data-form"), prompt = $(this).attr("data-prompt"), val, val1, name1,
                    name = $(this).attr("name") || $(this).attr("data-name"), location = $(this).attr("data-location") || "top",
                    promptOffset = $(this).attr("data-offset");

            switch (type) {
                case "select":
                    var selectedItem = $(this).find("li.selected");
                    val = selectedItem.attr("data-value");
                    if (val == null || val == undefined)
                        val = selectedItem.text();
                    break;
                case "radio":
                    val = $('[name="' + name + '"]:checked').val();
                    break;
                case "checkboxList":
                    var checkResult = new Array();
                    $(this).find(":checked").each(function () {
                        var _val = $(this).val();
                        if (_val !== '') {
                            checkResult.push(_val);
                        }
                    });
                    val = checkResult.join(",");
                    break;
                case "checkbox":
                    if (this.checked)
                        val = $(this).val();
                    else
                        val = $(this).attr("data-not-checked");
                    break;
                case "attribute":
                    val = $(this).attr("data-value");
                    break;
                case "timeRange":
                case "dateRange":
                    name = $(this).attr("data-name-start");
                    name1 = $(this).attr("data-name-stop");
                    var textboxs = $(this).find(":text");
                    val = textboxs.eq(0).val();
                    val1 = textboxs.eq(1).val();
                    break;
                case "custom":
                    val = window[$(this).attr("data-val")]($(this));
                    break;
                default:
                    val = $.trim($(this).val());
                    break;
            }
            if (ver) {
                var curItemResult = true
                ver = eval(ver), prompt = eval(prompt);
                for (var i = 0; i < ver.length; i++) {
                    if ($.type(ver[i]) == "string") {
                        if (ver[i] === "empty" || ver[i] === "startEmpty") {
                            if (!val)
                                curItemResult = false;
                        } else if (ver[i] === "stopEmpty") {
                            if (!val1)
                                curItemResult = false;
                        } else if (ver[i].indexOf("min") == 0) {
                            if (Number(val) < Number(ver[i].replace("min", "")))
                                curItemResult = false;
                        } else if (ver[i].indexOf("max") == 0) {
                            if (Number(val) > Number(ver[i].replace("max", "")))
                                curItemResult = false;
                        }else {
                            var val2 = $.trim($(ver[i]).val());
                            if (val2 != val)
                                curItemResult = false;
                        }
                    } else if ($.isFunction(ver[i].test)) {
                        if (!ver[i].test(val) && val)
                            curItemResult = false;
                    }

                    var promptDom = this;
                    if (promptOffset) {
                        switch (promptOffset) {
                            case "lastChild":
                                promptDom = $(this).children().last()[0];
                                break;
                            default:
                                if (promptOffset.indexOf("#") === 0)
                                    promptDom = $(promptOffset)[0];
                                else
                                    promptDom = $(this).find(promptOffset)[0];
                                break;
                        }
                    }

                    if (curItemResult) {
                        result.data[name] = val;
                        if (type === "dateRange" || type === "timeRange")
                            result.data[name1] = val1;

                        tools.closeTips(promptDom);
                    } else {
                        tools.tips(prompt[i], promptDom, {location: location});
                        result.result = false;
                        break;
                    }
                }
            } else {
                result.data[name] = val;
                if (type === "dateRange")
                    result.data[name1] = val1;
            }
        });
    }

    if (!result.result)
        delete result.data;

    return result;
};

/*
 * 工具代码
 */
(function () {
    var tipsIndex = 1, promptIndex = 1;
    window.tools = {
        merges: function () {                                   //$.merge升级版，可以同时合并多个对象到数组
            if (arguments.length) {
                if (arguments.length == 1)
                    return arguments[0];

                var result = arguments[0];
                if (!result.length) {
                    result.push({});
                }

                for (var i = 1; i < arguments.length; i++) {
                    if (arguments[i].length != 0) {
                        result = $.merge(result, arguments[i]);
                    } else {
                        result = $.merge(result, [{}]);
                    }
                }

                return result;
            }
            return $();
        },
        uploadImg: function (wh, callback, fileChange) {
            var iframe = document.createElement("iframe"), isHandle = false, isUploading = false;
            iframe.style.display = "none";
            document.body.appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write('<form method="post" enctype="multipart/form-data" action="/common/upload"><input accept="image/gif, image/jpeg, image/png, image/jpg, image/bmp" name="userfile" type="file" /><input name="wh" value="' + wh + '" type="hidden" /></form>');
            iframe.contentDocument.close();

            iframe.contentDocument.getElementsByTagName("input")[0].addEventListener("change", function () {
                if (this.files.length) {
                    if (/\.jpg|\.png|\.bmp|\.gif$/.test(this.files[0].name)) {
                        if ($.isFunction(fileChange))
                            fileChange.call(this, me);
                    } else {
                        tools.msg("所选文件类型不支持！");
                        iframe.contentDocument.getElementsByTagName("form")[0].reset();
                    }
                }
            });

            window._UPLOAD_IMG_CALLBACK = function (result) {
                console.log('_UPLOAD_IMG_CALLBACK');
                console.log(isHandle);
                console.log(result);
                if (!isHandle) {
                    if (/^\{\S+\}$/.test(result)) {
                        console.log('test');
                        if ($.isFunction(callback))
                            callback.call(me, eval("(" + result + ")"));
                        isHandle = true;
                    } else if (result !== "") {
                        console.log('   null');
                        if ($.isFunction(callback)) {
                            var ret = result.split("").reverse().join("").substr(3);
                            callback.call(me, {error: "图片格式不正确！[正确规格为" + wh + "],您上传的图片规格为：" + ret.split("").reverse().join("").substr(21)});
                        }
                        isHandle = true;
                    }
                }

                updateState();
            };

            function updateState() {
                if (isHandle || !iframe || !iframe.contentDocument.getElementsByTagName("input").length) {
                    isHandle = true;

                    me.allowOperation = false;
                    isUploading = false;
                    document.body.removeChild(iframe);
                    iframe = undefined;
                }
            }

            var me = {
                iframe: iframe,
                openSelect: function () {
                    me.iframe.contentDocument.getElementsByTagName("input")[0].click();
                },
                upload: function () {
                    if (!isUploading) {
                        isUploading = true;

                        me.iframe.contentDocument.getElementsByTagName("form")[0].submit();

                        setTimeout(function () {
                            if (!isHandle) {
                                isHandle = true;
                                document.body.removeChild(iframe);

                                if ($.isFunction(callback))
                                    callback.call(me, {error: "数据响应超时！"});

                                updateState();
                            }
                        }, 70000);
                    }
                },
                allowOperation: true
            }

            return me;
        },
        alert: function (text, title, callback) {
            var _title;
            if ($.isFunction(title)) {
                _title = callback;
                callback = title;
                title = undefined;
            }

            if (typeof _title === "string")
                title = _title;

            var meID = layer.alert(text, {
                closeBtn: 0,
                title: title || "信息提示框"
            }, function () {
                var isCloseMe = true;
                if ($.isFunction(callback))
                    isCloseMe = callback();

                if (isCloseMe !== false)
                    layer.close(meID);
            });

            return meID;
        },
        isMobile: function (text) {
            return /^1[34578][0-9]{9}$/.test(text);
        },
        loading: function (text) {
            layer.load(2);
        },
        closeLoading: function () {
            layer.closeAll('loading');
        },
        confirm: function (texts, okCallback, cancelCallback) {
            texts = texts || "";
            var promptText = $.type(texts) == "string" ? texts : texts.prompt;
            var meID = layer.confirm(promptText, {
                btn: [texts.ok || "确定", texts.ok || "取消"],
                title: texts.title || "操作询问框"
            }, function () {
                var isCloseMe = true;
                if ($.isFunction(okCallback))
                    isCloseMe = okCallback();

                if (isCloseMe !== false)
                    layer.close(meID);
            }, function () {
                if ($.isFunction(cancelCallback))
                    cancelCallback();
            });
        },
        tips: function (text, container, pars) {
            if (text && container) {
                pars = pars || {};
                pars.color = pars.color || "#fff";
                pars.background = pars.background || "#78BA32";
                pars.time = pars.time || 4000;
                pars.location = pars.location || "top";

                if ($.type(container) === "string" || !container.length)
                    container = $(container);

                if (!container.length)
                    return;

                var tipsID = container.attr("data-tips"), meIdx = tipsIndex++, offset = container.attr("data-tips", meIdx).offset(), rowCount = 0;

                if (tipsID)
                    $("#divTipsBox" + tipsID).remove();

                if (!$("#divTipsContainer").length)
                    $(document.body).append("<div id='divTipsContainer'></div>");

                var html = '<div style="background:' + pars.background + ';color:' + pars.color + ';left:{offsetX}px;top:{offsetY}px;" class="ipm-tips ipm-tips-' + pars.location + '" id="divTipsBox' + meIdx + '">';
                $.each(text.split("\n"), function () {
                    html += '<div>' + this.toString() + '</div>';
                    rowCount++;
                });

                var borderLocation, offsetX, offsetY;
                switch (pars.location) {
                    case "left":
                        borderLocation = "border-bottom-color";
                        offsetX = -container.innerWidth() - 13;
                        offsetY = offset.top + (container.innerHeight() - 10 - rowCount * 14) / 2;
                        break;
                    case "right":
                        borderLocation = "border-bottom-color";
                        offsetX = offset.left + container.innerWidth() + 13;
                        offsetY = offset.top + (container.innerHeight() - 10 - rowCount * 14) / 2;
                        break;
                    case "bottom":
                        borderLocation = "border-right-color";
                        offsetX = offset.left;
                        offsetY = offset.top + container.innerHeight() + 10;
                        break;
                    default:
                        offsetX = offset.left;
                        offsetY = offset.top - 20 - rowCount * 14;
                        borderLocation = "border-right-color";
                        break;
                }

                html += '<i style="' + borderLocation + ':' + pars.background + ';"></i></div>';

                $("#divTipsContainer").append(html.replace("{offsetY}", offsetY).replace("{offsetX}", offsetX));

                if (pars.location === "left")
                    $("#divTipsBox" + meIdx).css("left", offset.left - $("#divTipsBox" + meIdx).innerWidth() - 13 + "px");

                setTimeout(function () {
                    $("#divTipsBox" + meIdx).remove();
                }, pars.time);

                return meIdx;
            }
        },
        closeTips: function (container) {
            if (container) {
                if (isNaN(container)) {
                    if ($.type(container) === "string" || !container.length)
                        container = $(container);

                    if ($.isFunction(container.attr))
                        $("#divTipsBox" + container.attr("data-tips")).remove();
                } else if (container) {
                    $("#divTipsBox" + container).remove();
                }
            } else {
                $("#divTipsContainer").empty();
            }
        },
        prompt: function (title, defaultValue, callback, pars) {
            var _defaultValue;
            if ($.isFunction(defaultValue)) {
                _defaultValue = callback;
                callback = defaultValue;
                defaultValue = undefined;
            }

            if (typeof _defaultValue === "string")
                defaultValue = _defaultValue;

            title = title || "信息输输入框";
            defaultValue = defaultValue === undefined ? "" : defaultValue;

            pars = pars || {};
            pars.type = pars.type || "text";
            pars.attr = pars.attr || "";

            var layerConfig = {
                title: title,
                type: 1,
                area: ['270px', '155px'], //宽高,
                btn: ['确定', '取消'],
                content: '<input type="' + pars.type + '" ' + pars.attr + ' value="' + defaultValue + '" class="layui-custom-input" id="txtLayoutPrompt' + promptIndex++ + '" />',
                yes: function () {
                    var val = $.trim(textbox.val());
                    if (val) {
                        var isClose;
                        if ($.isFunction(callback))
                            isClose = callback(val, textbox[0]);
                        if (isClose !== false)
                            layer.close(promptId);
                    }
                },
                closeBtn: 0,
                cancel: function () {
                    if ($.isFunction(pars.cancel))
                        pars.cancel(textbox[0]);
                }
            };

            if (pars.move !== undefined && !pars.move)
                layerConfig.move = false;

            var promptId = layer.open(layerConfig), textbox = $("#txtLayoutPrompt" + (promptIndex - 1));

            tools.moveToEnd(textbox[0]);

            return textbox[0];
        },
        moveToEnd: function (textbox) {
            var len = textbox.value.length;
            if (document.selection) {
                var sel = textbox.createTextRange();
                sel.moveStart('character', len);
                sel.collapse();
                sel.select();
            } else if (typeof textbox.selectionStart == 'number' && typeof textbox.selectionEnd == 'number') {
                textbox.setSelectionRange(len, len);
                textbox.focus();
            }
        },
        isHiddle: function (dom) {
            if (!dom)
                return false;

            if (dom.length === undefined)
                dom = $(dom);

            while (dom && dom[0].tagName !== "BODY") {
                var meDom = $(dom);
                if (meDom.css("display") !== "none")
                    dom = meDom.parent();
                else
                    return true;
            }
            return false;
        },
        msg: function (text) {
            layer.msg(text || "提示信息！");
        }
    };
})();

/*
 * document的click事件，用来处理一些杂项
 * 如：点击时把所有的下拉框全部收起来。
 */
if (document.addEventListener) {
    document.addEventListener("click", function () {
        $("div.ipm-select-box.active").removeClass("active");       //隐藏被下拉了的select控件选择项。
    }, true);
}

function hidecity() {
    var b = $("#b_dataPicker").val();
    var e = $("#e_dataPicker").val();
    console.log("hidecity " + b + " " + e);
    if (b != e) {
        $("#selTag").css("opacity",0);
    } else {
        $("#selTag").css("opacity",1);
    }
}


/**
 * 选择日期后需要跳转
 * @author kerwin
 */

function _redirect(controller) {
    var b = $("#b_dataPicker").val();
    var e = $("#e_dataPicker").val();
    var host = $("#container").attr("host");

    var ad_type = $("#selAdType").find("li.selected").attr("data-value");
    var selTag = $("#selTag").find("li.selected").attr("data-value");
    var hdsetreport = $("#setType").attr("hdsetreport");
    console.info(controller);
     var scenes = 0;
    if ($("#cc")[0]) {
        var val = $('#cc').combotree('tree');
        console.info(val);
        var nodes = val.tree('getChecked');
       scenes = '';
       var havedot = false;
        for (var i = 0; i < nodes.length; i++) {
            if (scenes != ''){
                havedot = true;
            }

            console.info(nodes[i].parent_id);
            if(nodes[i].parent_id >0){
                if(havedot){
                                    scenes += ',';
                }
            scenes += nodes[i].id;}
        }
        console.info(scenes);
    }
    var url;
    if (controller == "home") {
        var ad = $("#selAdBelong").find("li.selected").attr("data-value");


        url = host + controller + "/ajax_index";
        if (typeof (ad) == "undefined")
            return;
    } else if (controller == "report") {
        var ad = $("#selAdState").find("li.selected").attr("data-value");
        if (typeof (ad) == "undefined")
            return;
        controller = "home";
        url = host + controller + "/ajax_report";
    } else if (controller == "hdhome") {
        //var ad = $("#selAdBelong").find("li.selected").attr("data-value");

        //var ad  = $("#selAdBelong").multiselect("MyValues");
        //alert('ad='+ad);

        var value = $("#selAdBelong").multiselect("getChecked").map(function () {
            return this.value;
        }).get();

        var ad = value.join(",");

        var ad_type = $("#selAdMain").val();

        url = host + controller + "/ajax_index";
        if (typeof (ad) == "undefined")
            return;
    } else if (controller == "hdreport") {
        var ad = $("#selAdState").find("li.selected").attr("data-value");
        if (typeof (ad) == "undefined")
            return;
        controller = "hdhome";
        url = host + controller + "/ajax_report?hdsetreport=" + hdsetreport;
    }
    //var url = host + controller + "/ajax_index";
    var data = {
        b_date: b,
        e_date: e,
        ad_type: ad_type, //set or apps
        sel_tag: selTag, //city
        ad_id: ad,
        scenes: scenes
    }

    $.ajax({
        url: url,
        dataType: "json",
        type: "POST",
        data: data,
        success: function (data) {
            $("#ad_bg").text(Number(data.ad.bg));
            $("#ad_ctr").text(Number(data.ad.pv));
            $("#ad_ctr_per").text(data.ad.ctr_per);
            $("#ad_user_money").text(Number(data.ad.useMoney));
            $('#divData').highcharts(JSON.parse(data.chart));

            var html = "";
            $.each(data.detail, function (i, index) {
                html += '<div class="ipm-table-row">';
                html += '<div data-name="time" class="ipm-table-cell1">' + index[0] + '</div>';
                html += '<div data-name="bg">' + index[1] + '</div>';
                html += '<div data-name="pv">' + index[2] + '</div>';
                html += '<div data-name="uv">' + index[3] + '</div>';
                html += '<div data-name="click">' + index[4] + '</div>';
                html += '<div data-name="cli_lv">' + index[5] + '</div>';
                html += '<div data-name="cost_per">' + index[6] + '</div>';
                html += '<div data-name="cost">' + index[7] + '</div>'
                html += '<div data-name="finished">' + index[8] + '</div>'
                html += '<div data-name="hbmoney">' + index[9] + '</div>'
                //modify by maofei  2016-11-7  将报表生成的M币、红包金额分别统计
                html += '<div data-name="mbmoney">' + index[10] + '</div>'
                html += "</div>";
            })

            $("#ad_detail_data").find(".ipm-table-row").remove();
            $("#ad_detail_data").append(html);
        },
        error: function () {

        }
    })
}
;

/**
 * 选择日期后需要跳转
 * @author kerwin
 */

function hdindex(controller) {

    var data = {
        b_date: b,
        e_date: e,
        ad_type: ad_type,
        sel_tag: selTag,
        ad_id: scenes
    }

    $.ajax({
        url: url,
        dataType: "json",
        type: "POST",
        data: data,
        success: function (data) {
            $("#ad_bg").text(Number(data.ad.bg));
            $("#ad_ctr").text(Number(data.ad.pv));
            $("#ad_ctr_per").text(data.ad.ctr_per);
            $("#ad_user_money").text(Number(data.ad.useMoney));
            $('#divData').highcharts(JSON.parse(data.chart));

            var html = "";
            $.each(data.detail, function (i, index) {
                html += '<div class="ipm-table-row">';
                html += '<div data-name="time" class="ipm-table-cell1">' + index[0] + '</div>';
                html += '<div data-name="bg">' + index[1] + '</div>';
                html += '<div data-name="pv">' + index[2] + '</div>';
                html += '<div data-name="uv">' + index[3] + '</div>';
                html += '<div data-name="click">' + index[4] + '</div>';
                html += '<div data-name="cli_lv">' + index[5] + '</div>';
                html += '<div data-name="cost_per">' + index[6] + '</div>';
                html += '<div data-name="cost">' + index[7] + '</div>'
                html += '<div data-name="finished">' + index[8] + '</div>'
                html += '<div data-name="hbmoney">' + index[9] + '</div>'
                //modify by maofei  2016-11-7  将报表生成的M币、红包金额分别统计
                html += '<div data-name="mbmoney">' + index[10] + '</div>'
                html += "</div>";
            })

            $("#ad_detail_data").find(".ipm-table-row").remove();
            $("#ad_detail_data").append(html);
        },
        error: function () {

        }
    })
}

function addLoadEvent(func) {
    var oldonload = window.onload;
    if (typeof window.onload != 'function') {
        window.onload = func;
    } else {
        window.onload = function () {
            oldonload();
            func();
        }
    }
}
// if (outdatedBrowser) {
//     //call function after DOM ready
//     addLoadEvent(
//             outdatedBrowser({
//                 bgColor: '#222',
//                 color: '#c77405',
//                 lowerThan: 'borderImage',
//                 languagePath: "http://" + location.host + '/www/plugs/outdatedbrowser/lang/zh-cn.html'
//             })
//             );
// }

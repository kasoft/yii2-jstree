/* Version 0.9 */

// ACTIVE RECORD VERSION
if (typeof jsonurl === 'undefined') {
    
    if(url_default.indexOf('?')<0) var chainCharakter = '?'; 
    else var chainCharakter = '&'
    var jsonurl = url_default + chainCharakter + "easytree=fulljson";

    $.getJSON(jsonurl, function (jsdata) {
        $(jstreediv).jstree({
            "core": {
                "animation": 0,
                "check_callback": true,
                strings : {
                    'New node': jstreeMsg.newNode
                },
                "themes": {
                    "stripes": true,
                    "icons": jstreeicons
                },
                'data': jsdata
            },
            "types": jstreetype,
            "state": { "key": jstreestatekey },
            "plugins": jstreeplugins,
            "contextmenu": {
                "items": function (data) {
                    var context_items = {};
                    
                    // Edit Menü 
                    if ('edit' in jstreeContextMenue) {
                        var edit = jstreeContextMenue.edit;
                        context_items.edit = {
                                "label": edit.text,
                                "action": function (data) {
                                    var inst = $.jstree.reference(data.reference);
                                    obj = inst.get_node(data.reference);
                                    // tbd
                                    // allow both methods: ajax load und href link
                                    // location.href = url_default +'/update?id=' + obj.id.replace("id", "");

                                    // If .result div exists, use ajax, otherwise redirect
                                    if ($('.jstree-result').length){
                                        $.ajax({
                                            type: "GET",
                                            url: url_click + chainCharakter + 'id=' + obj.id.replace("id", ""),
                                            success: function (data, textStatus) {
                                                $(".result").html(data);
                                            },
                                            error: function () {
                                                alert('Error loading Page!');
                                            }
                                        });
                                    } else {
                                        window.location = url_click + chainCharakter + 'id=' + obj.id.replace("id", "");
                                    }
                                }
                        }
                        if(typeof edit.icon!=="undefined") context_items.edit.icon = edit.icon;
                        else context_items.edit.icon = "glyphicon glyphicon-pencil";
                    }
                    
                    // Create/New Menü 
                    if ('create' in jstreeContextMenue) {
                        var create = jstreeContextMenue.create;
                        
                        //Submenu with different Types
                        if(typeof create.submenu!=='undefined') {
                                var subitems = {};
                                create.submenu.forEach(function (element, index) {
                                    subitems['create_'+index] = {
                                        "label": element.text,
                                        "action": function (data) {
                                            var inst = $.jstree.reference(data.reference),
                                            obj = inst.get_node(data.reference);
                                            var new_node_id = inst.create_node(obj, { type : element.type }, "last", function (new_node) {
                                                    setTimeout(function () { inst.edit(new_node); },0);
                                            });
                                            if(new_node_id===false) alert(jstreeMsg.nothere);
                                        }
                                    };
                                    if(typeof element.icon!=="undefined") subitems['create_'+index]['icon'] = element.icon;
                                });
                                context_items.create = {
                                    "label": create.text,
                                    "submenu": subitems
                                };
                            
                        // Single Menue    
                        } else {
                            var node_type = "default";
                            if(typeof create.type!=='undefined') node_type=create.type;
                            context_items.create = {
                                "label": jstreeContextMenue.create.text,
                                "action": function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                    var new_node_id = inst.create_node(obj, { type : node_type }, "last", function (new_node) {
                                            setTimeout(function () { inst.edit(new_node); },0);
                                    });
                                    if(new_node_id===false) alert(jstreeMsg.nothere);
                                }
                            }
                            
                        }
                        if(typeof create.icon!=="undefined") context_items.create.icon = create.icon;
                        else context_items.create.icon = "glyphicon glyphicon-th-list";
                    };
                    
                    // Rename Menu
                    if ('rename' in jstreeContextMenue) {
                        var rename = jstreeContextMenue.rename;
                        context_items.rename = {
                            "label": rename.text,
                             "action": function (data) {
                                var inst = $.jstree.reference(data.reference);
                                obj = inst.get_node(data.reference);
                                inst.edit(obj);
                            }
                        }
                        if(typeof rename.icon!=="undefined") context_items.rename.icon = rename.icon;
                        else context_items.rename.icon = "glyphicon glyphicon-transfer";
                    };
                    
                    // Duplicate Menu
                    if ('duplicate' in jstreeContextMenue) {
                        var duplicate = jstreeContextMenue.duplicate;
                        context_items.duplicate = {
                            "label": duplicate.text,
                             "action": function (data) {
                                var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                                obj_parent = inst.get_parent(obj);
                                var new_node_id = inst.create_node(obj_parent, { type : obj.type, icon: obj.icon, duplicate_id: obj.id.replace("id", ""), text: obj.text+" Kopie" }, "last");
                                if(new_node_id===false) alert(jstreeMsg.nothere);
                            }
                        }
                        if(typeof duplicate.icon!=="undefined") context_items.duplicate.icon = duplicate.icon;
                        else context_items.duplicate.icon = "glyphicon glyphicon-duplicate";
                    };
                    
                    // Delete Menu 
                    if ('remove' in jstreeContextMenue) {
                        var remove = jstreeContextMenue.remove;
                        context_items.remove = {
                            "label": remove.text,
                            "action": function (data) {
                                if (confirm(jstreeMsg.confirmdelete)) {
                                    var inst = $.jstree.reference(data.reference);
                                    selected = inst.get_selected(data.reference);
                                    for (var key in selected) {
                                        // skip loop if the property is from prototype
                                        if (!selected.hasOwnProperty(key)) continue;
                                        var obj = selected[key];
                                        $.ajax({
                                            async: false,
                                            type: 'POST',
                                            dataType: "json",
                                            url: url_default,
                                            data: {
                                                "easytree": "delete",
                                                "id": obj.id.replace("id", ""),
                                            },
                                            success: function (r) {
                                                if (r.status) {
                                                    inst.delete_node(obj);
                                                } else {
                                                    alert(r.error);
                                                    return false;
                                                }
                                            }
                                        });

                                    }
                                }
                            }
                        }
                        if(typeof remove.icon!=="undefined") context_items.remove.icon = remove.icon;
                        else context_items.remove.icon = "glyphicon glyphicon-trash";
                    };
                    
                    // check if contect menu elements should be removed because of a special node type
                    // e.g. create is not allowd for type "page"
                    var keyitems = Object.keys(context_items);
                    keyitems.forEach(function(ele,i) {
                       var check_item = jstreeContextMenue[ele];
                       if(typeof check_item.disallow_type!=='undefined') {
                       check_item.disallow_type.forEach(function (element, index) {
                            if(data.type==element) delete context_items[ele];
                       });
                       }
                    });
                    
                    return context_items;
                }
            },
        }).on("move_node.jstree", function (e, data) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: "json",
                url: url_default,
                data: {
                    "easytree": "move",
                    "id": data.node.id.replace("id", ""),
                    "position": data.position,
                    "parent": data.parent.replace("id", ""),
                },
                success: function (r) {
                    if (!r.status) {
                        // rollback v3 ??
                        // $.jstree.rollback(data.rlbk);
                    }
                }
            });
        }).on("create_node.jstree", function (e, data) {
            $.ajax({
                async: false,
                type: 'POST',
                url: url_default,
                dataType: "json",
                data: {
                    "easytree": "create",
                    "id": data.node.id.replace("id", ""),
                    "position": data.position,
                    "type": data.node.type,
                    "text": data.node.text,
                    "parent": data.parent.replace("id", ""),
                    "duplicate" : data.node.original.duplicate_id,
                },
                success: function (r) {
                    if (r.status) {
                        data.instance.set_id(data.node.id, r.id)
                    } else {
                        if(typeof r.err_msg!=="undefined") alert(r.err_msg);
                        data.instance.delete_node(data.node);
                    }
                }
            });
        }).on("rename_node.jstree", function (e, data) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: "json",
                url: url_default,
                data: {
                    "easytree": "rename",
                    "id": data.node.id.replace("id", ""),
                    "text": data.text,
                },
                success: function (r) {
                    if (!r.status) {
                        // rollback v3 ??
                        // $.jstree.rollback(data.rlbk);
                    }
                }
            });
        }).on("select_node.jstree", function (e, data) {
            if ($('.jstree-result').length){
                $.ajax({
                    type: "GET",
                    url: url_click + chainCharakter +'id=' + data.node.id.replace("id", ""),
                    success: function (data, textStatus) {
                        $(".jstree-result").html(data);
                        if (typeof afterLoad === "function") {
                            afterLoad();
                        }
                    },
                    error: function () {
                        // alert('Error loading Page!');
                    }
                });
            } 
        });
    });


    /************* TREE ACTION ******************/

    /* Submitting Form Content should be send to .jstree-result div */
    $(document).on('submit', 'form.jstree-form', function (event) {
        $(".jstree-result").prepend('<div class="jstree-result-loader"><p>Sende Daten ...</p></div>');
        $.ajax({
            data: $(this).serialize(), // get the form data
            type: $(this).attr('method'), // GET or POST
            url: $(this).attr('action'), // the file to call
            success: function (response) { // on success..
                $('.jstree-result').html(response); // update the DIV
            }
        });
        return false; // cancel original event to prevent form submitting
    });

    /* Buttons or Links in Tree Form should load in result div */
    $(document).on('click', '.jstree-button', function (event) {
        // if confirm is set (e.g. delete action) involve confirm dialog 
        var doit = true;
        if ($(this).data('doconfirm')) {
            if (confirm($(this).data('doconfirm'))) doit=true;
            else doit=false;
        }
        if(doit) {
            $(".jstree-result").prepend('<div class="jstree-result-loader"><p>Sende Daten ...</p></div>');
            $.ajax({
                type: "GET",
                url: $(this).attr('href'),
                success: function (response) {
                    $('.jstree-result').html(response);
                }
            });
        }
        return false; // stop the browser following the link
    });

    /* Tree click Preloader */
    /* Every klick on a treeitem load the update in resonse div*/
    $(document).ready(function () {
        if ($('.jstree-result').length){
            $(jstreediv).on("select_node.jstree", function (e, data) {
                $(".jstree-result").prepend('<div class="jstree-result-loader"><p>Lade Daten ...</p></div>');
            });
        }
    });

// JSON ONLY VERSION
} else {

    $.getJSON(jsonurl, function (jsdata) {
        $(jstreediv).jstree({
            "core": {
                "animation": 0,
                "check_callback": true,
                "themes": {
                    "stripes": true,
                },
                'data': jsdata
            },
            "checkbox": {
                "keep_selected_style" : false,
                "three_state": false,
            },
            "plugins": [
                "types", "wholerow", "checkbox"
            ]
        })
    });
    
    $(document).on('submit','form',function(event){
        var selectedElmsIds = $(jstreediv).jstree("get_selected");
        console.log(selectedElmsIds);
        $('<input>').attr({
            type: 'hidden',
            id: 'jstree-checkboxes',
            name: 'jstree-checkboxes',
            value: selectedElmsIds.join()
        }).appendTo('form');
    });

}
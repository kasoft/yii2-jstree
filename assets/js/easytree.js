/* Version 0.9 */

// ACTIVE RECORD VERSION
if (typeof jsonurl === 'undefined') {
    // the variable is defined
    var base_url = "/" + controller + "/";
    var jsonurl = base_url + index_action + "?easytree=fulljson";

    $.getJSON(jsonurl, function (jsdata) {
        $(jstreediv).jstree({
            "core": {
                "animation": 0,
                "check_callback": true,
                "themes": {
                    "stripes": true,
                    "icons": false
                },
                'data': jsdata
            },
            "types": jstreetype,
            "plugins": jstreeplugins,
            "contextmenu": {
                "items": function () {
                    return {
                        "Edit": {
                            "label": "Bearbeiten",
                            "icon": "fa fa-pencil",
                            "action": function (data) {
                                var inst = $.jstree.reference(data.reference);
                                obj = inst.get_node(data.reference);
                                // tbd
                                // allow both methods: ajax load und href link
                                // location.href = base_url +'/update?id=' + obj.id.replace("id", "");
                                $.ajax({
                                    type: "GET",
                                    url: base_url + '/update?id=' + obj.id.replace("id", ""),
                                    success: function (data, textStatus) {
                                        $(".result").html(data);
                                    },
                                    error: function () {
                                        alert('Error loading Page!');
                                    }
                                })
                            }
                        },
                        "Create_menue": {
                            "label": "Neu",
                            "icon": "glyphicon glyphicon-th-list",
                            "action": function (data) {
                                var ref = $.jstree.reference(data.reference);
                                sel = ref.get_selected();
                                if (!sel.length) {
                                    return false;
                                }
                                sel = sel[0];
                                sel = ref.create_node(sel, {"type": "menue"});
                                if (sel) {
                                    ref.edit(sel);
                                }
                            }
                        },
                        "Rename": {
                            "label": "Umbenennen",
                            "icon": "fa fa-refresh",
                            "action": function (data) {
                                var inst = $.jstree.reference(data.reference);
                                obj = inst.get_node(data.reference);
                                inst.edit(obj);
                            }
                        },
                        "Delete": {
                            "label": "Löschen",
                            "icon": "fa fa-trash",
                            "action": function (data) {
                                if (confirm("Sind Sie sicher?")) {
                                    var inst = $.jstree.reference(data.reference);
                                    obj = inst.get_node(data.reference);
                                    $.ajax({
                                        async: false,
                                        type: 'POST',
                                        dataType: "json",
                                        url: base_url,
                                        data: {
                                            "easytree": "delete",
                                            "id": obj.id.replace("id", ""),
                                        },
                                        success: function (r) {
                                            if (r.status) {
                                                var ref = $.jstree.reference(data.reference);
                                                sel = ref.get_selected();
                                                if (!sel.length) {
                                                    return false;
                                                }
                                                ref.delete_node(sel);
                                            } else {
                                                alert(r.error);
                                                return false;
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    };
                }
            },
        }).on("move_node.jstree", function (e, data) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: "json",
                url: base_url,
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
                url: base_url,
                dataType: "json",
                data: {
                    "easytree": "create",
                    "id": data.node.id.replace("id", ""),
                    "position": data.position,
                    "type": data.node.type,
                    "parent": data.parent.replace("id", ""),
                },
                success: function (r) {
                    if (r.status) {
                        data.instance.set_id(data.node.id, r.id)
                    } else {
                        // rollback v3 ??
                        // $.jstree.rollback(data.rlbk);
                    }
                }
            });
        }).on("rename_node.jstree", function (e, data) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: "json",
                url: base_url,
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
            $.ajax({
                type: "GET",
                url: base_url + 'update?id=' + data.node.id.replace("id", ""),
                success: function (data, textStatus) {
                    $(".jstree-result").html(data);
                    if (typeof afterLoad === "function") {
                        afterLoad();
                    }
                },
                error: function () {
                    // alert('Error loading Page!');
                }
            })

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
        $(".jstree-result").prepend('<div class="jstree-result-loader"><p>Sende Daten ...</p></div>');
        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            success: function (response) {
                $('.jstree-result').html(response);
            }
        });
        return false; // stop the browser following the link
    });

    /* Tree click Preloader */
    /* Every klick on a treeitem load the update in resonse div*/
    $(document).ready(function () {
        $(jstreediv).on("select_node.jstree", function (e, data) {
            $(".jstree-result").prepend('<div class="jstree-result-loader"><p>Lade Daten ...</p></div>');
        });
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
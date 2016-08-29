/* Version 0.9 */


if (typeof jsonurl === 'undefined') {
    // the variable is defined
    var base_url = "/" + controller + "/";
    var tree_fulljson = base_url + index_action + "?easytree=fulljson";
} 

$.getJSON(jsonurl, function (jsdata) {
    $('#jstree').jstree({
        "core": {
            "animation": 0,
            "check_callback": true,
            "themes": {
                "stripes": true,
                "icons": show_icons
            },
            'data': jsdata
        },
        "types": {
            "#": {
                "max_children": -1,
                "max_depth": -1,
                "valid_children": -1, // "valid_children": ["root","xyz","folder"]
                "icon": "glyphicon glyphicon-th-list"
            },
            "page": {
                "icon": "glyphicon glyphicon-file",
            },
            "menue": {
                "icon": "glyphicon glyphicon-th-list",
            },
            "link": {
                "icon": "glyphicon glyphicon-log-in",
                "max_children": 0,
            },
            "default": {
                "icon": "glyphicon glyphicon-question-sign",
            }
        },
        "plugins": [
            "contextmenu", "dnd", "search",
            "state", "types", "wholerow", "changed"
        ],
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
            url: base_url+'update?id=' + data.node.id.replace("id", ""),
            success: function (data, textStatus) {
                $(".jstree-result").html(data);
            },
            error: function () {
                // alert('Error loading Page!');
            }
        })

    });
});


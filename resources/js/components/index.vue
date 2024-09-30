<template>
    <navigation
        :baseimage="props.baseimage"
        :data="props.data">
    </navigation>
    <section>
        <div class="flex flex-col pt-40 w-full">
            <div class="bg-gradient-to-r from-[#1c1936] to-[#482a59] w-screen px-1 py-4 text-slate-50">
                <div class="flex flex-row justify-start w-1/3 ">
                    <div class="flex justify-center items-center">
                        <a href="#" class="px-3">Dashboard</a>
                        <a href="#" class="px-3">Transaction</a>
                    </div>
                </div>
            </div>
            <div class="flex justify-end items-start p-3 w-full">
                <button type="button" class="button-warning" @click="openModal()">
                    Create New Record
                </button>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="flex flex-col justify-start items-start mx-3 px-2 py-1  w-1/5 rounded-lg">
                <div class="w-full  px-2 py-3">
                    <label class="block text-slate-50 text-sm font-bold py-1" for="username">
                        STATUS 
                    </label>
                    <treeselect 
                        :multiple="false" 
                        :options="props.data.statusOption" 
                        :normalizer="state.normalizer" 
                        placeholder="Please select Status" 
                        v-model="state.tableFilter.status"/>
                    <label class="block text-slate-50 text-sm font-bold py-1" for="username">
                        PRIORITY LEVEL 
                    </label>
                    <treeselect 
                        :multiple="false" 
                        :options="props.data.priorityOption" 
                        :normalizer="state.normalizer" 
                        placeholder="Please select Priority Level" 
                        v-model="state.tableFilter.priority_level"/>
                </div>
                <div class="flex justify-start items-start py-2">
                    <button type="button" class="button-danger" id="generateButton">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </section>
    <section>
       <div class="flex w-full  p-3">
            <div class="w-full" >
                <table class="min-w-full divide-y divide-gray-200 " id="taskTable">
                    <thead class="text-center">
                        <tr class="text-slate-50">
                            <th data-index="title" scope="col" class="px-6 border-2  py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Title</th>
                            <th data-index="description" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Description</th>
                            <th data-index="due_date" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Due Date</th>
                            <th data-index="archive_date" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Archive Date</th>
                            <th data-index="priority_level" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Priority Level</th>
                            <th data-index="attachment" scope="col" class="px-6  border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">ATTACHMENT</th>
                            <th data-index="tasks.created_at" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Created Date</th>
                            <th data-index="status" scope="col" class="px-6  border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">STATUS</th>
                            <th data-index="action" scope="col" class="px-6 border-2 py-3 text-center text-xs font-mediumtext-slate-50 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                    </tbody>
                </table>
            </div>
       </div>
    </section>
    <taskModal 
        :baseimage="props.baseimage"
        :data="props.data"
        :openmodal="state.modal.isModalOpen"
        :details="state.payload"
        @isClosed="handleConfirm">
    </taskModal>
</template>
<script setup>
import taskModal from "./modal/info.vue";
import navigation from "./nav.vue";
import moment from 'moment';
import Treeselect from 'vue3-treeselect';
import { reactive, onMounted , onUpdated} from "vue";
const props = defineProps(["baseimage", "data", "storagepath"]);

const state = reactive({
    modal: {
        isModalOpen: false
    },
    payload: {
        id: null,
        title: null,
        status: null,
        priority_level: null,
        due_date: null,
        archive_date: null,
        tags: [],
        attachment: [],
    },
    tableFilter: {
        titleSearch: [],
        status: null,
        priority_level: null,
    },
    normalizer(node) {
        return {
            id: node.code,
            label: node.label,
            children: node.subOptions,
        }
    },
});

onMounted(() => {
    loadTable();
    openDetail();
});

const openModal = () => {
    clearFields();
    state.modal.isModalOpen = true;
}

const handleConfirm = (data) => {
    if (data) {
        state.modal.isModalOpen = false;
    }
}


const loadTable = () => {
    let tableparam = [];
    $('#taskTable thead tr').clone(true).appendTo('#taskTable thead');
    $('#taskTable thead tr:eq(1) th').each( function (i) {
        let title = $(this).data('index');
        if (title !='action' && title !='attachment' && title !='tags' && title !='status') {
            $(this).html(
                '<input type="text"  class="input-field text-center" \
                data-index="'+$(this).data('index')+'" placeholder="Search"/>'
            );
        } else {
            $(this).html(
                '<input type="text" class="input-field text-center" disabled\
                data-index="'+$(this).data('index')+'"/>'
            );
        }
    });

    let taskTable = $('#taskTable').DataTable({
        lengthMenu: [[10,20, 30, 50], [10,20, 30, 50]],
        scrollX : true,
        scrollCollapse : true,
        serverSide : true,
        processing : true,
        ordering : true,
        destroy : true,
        stateSave: false,
        deferLoading : 0,
        responsive: true,
        ajax: $.fn.dataTable.pipeline({
            url: props.data.taskdataTableUrl,
            method: "POST",
            pages: 5,
            start: 0,
            data:function(item) {
                item._token = $('meta[name="csrf-token"]').attr('content');
                item.tableparam = tableparam;
                item.tableFilter = state.tableFilter;
            }
        }),
        columns : [
            { data : 'title' },
            { data : 'description' },
            { data : 'due_date' },
            { data : 'archive_date' },
            { data : 'priority_level_description' },
            { 
                sortable:false,
                render:function(data, type, full, meta) {
                    let tempData = [];
                    if (full.attachments.length> 0) {
                        full.attachments.map(function(item, index) {
                            let tempValue = " \
                                <a href="+props.data.attachmentUrl+'/'+item.file_name+" class='underline text-red-700 hover:text-red-900 my-1' target='_blank' download> "+item.file_name+" </a>";
                            let tempIndex = index + 1;
                            if (tempIndex % 2 === 0) {
                                tempValue = tempValue + "<br/>"
                            }
                            tempData.push(tempValue);
                        });
                    }

                    return tempData.join(" , ");
                }
            },
            { data : 'creation_date' },
            { data : 'status_description', sortable:false},
            { 
                sortable:false,
                render:function(data, type, full, meta) {
                    let attachmentData = [];
                    if (full.attachments.length> 0) {
                        full.attachments.map(function(item, index) {
                            attachmentData.push(item.file_name);
                        });
                    }
                    return " \
                        <button type='button' class='button-danger openDetail' \
                            data-id='"+full['id']+"' \
                            data-title='"+full['title']+"' \
                            data-description='"+full['description']+"' \
                            data-due_date='"+full['due_date']+"' \
                            data-archive_date='"+full['archive_date']+"' \
                            data-priority_level='"+full['priority_level']+"' \
                            data-attachments='"+attachmentData+"' \
                            data-status='"+full['status']+"'> \
                            View \
                        </button>\
                        <button type='button' class='button-warning deleteDetail' \
                            data-id='"+full['id']+"'> \
                            Delete \
                        </button>";
                }
            },
        ],
        dom:"<'row text-slate-50'<'sm md text-xs text-left mb-2 text-slate-50'l><'md sm text-center text-slate-50'>" +
            "<'row text-slate-50'<'md sm text-slate-50'tr>>" +
            "<'row pt-2 text-slate-50'<'md sm text-sm text-left text-slate-50'i><'md sm text-xs text-right text-slate-50'p>>"
    });

    $(taskTable.table().container()).on('keypress', 'thead input', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            let col = $(this).data('index');
            let search = this.value;
            runUpdateTable(col, search);
        }
    });    

    $(taskTable.table().container()).on('change', 'thead input', function (e) {
        let col = $(this).data('index');
        let search = this.value;
        let i = $.map(tableparam, function(item, i) {
            if (item.columnname == col)
            return i;
        })[0];
        runUpdateTable(col, search);
    });

    function runUpdateTable (col, search) {
        let i = $.map(tableparam, function(item,i) {
            if(item.column_name == col)
                return i;
        })[0];
        if (typeof i === 'undefined') {
            tableparam.push({column_name : col , column_value : search})
        } else {
            tableparam[i].column_value = search;
        }
    }

    $('#generateButton').on('click', function (e) {
        taskTable.clearPipeline().draw();
    });  

    taskTable.clearPipeline().draw();
}

const openDetail = () => {
    clearFields();
    $(function(){
        let isGuest = props.data.isGuestUser;
        $(document).on("click", ".openDetail", function() {
            if (!isGuest) {
                state.payload.id = $(this).attr("data-id");
                state.payload.title = $(this).attr("data-title");
                state.payload.description = $(this).attr("data-description");
               
                state.payload.priority_level = $(this).attr("data-priority_level");
                state.payload.status = $(this).attr("data-status");

                if (typeof undefined === $(this).attr("data-due_date")) {
                     state.payload.due_date = null;
                }

                let attachment = [];
                let tempAttachmentData = $(this).attr("data-attachments").split(',');
                if (tempAttachmentData.length > 0 && $(this).attr("data-attachments").length != 0) {
                    tempAttachmentData.map(function(item, index) {
                        let obj = {
                            name: item
                        }

                        attachment.push(obj);
                    });
                }
                state.payload.attachment = attachment;
                state.modal.isModalOpen = true;
            } else {
                handlePage(401);
            }
        });

        $(document).on("click", ".deleteDetail", function() {
            if (!isGuest) {
                axios({
                    method: "DELETE",
                    url: props.data.taskUpdateUrl+"/"+$(this).attr("data-id"),
                    data: {
                        payload: props.details
                    },
                }).then(function (response) {
                    if (response.status == 200) {
                        location.reload(); 
                    }
                })
                .catch(function (errorHandler) {
                    if (errorHandler.response.status == 422) {
                        state.errors = errorHandler.response.data.errors;
                    }
                }).finally(function () {
                    location.reload(); 
                });  
            } else {
                handlePage(401);
            }
        });
        
    })
}

const clearFields = () => {
    state.payload.id = null;
    state.payload.title = null;
    state.payload.status = null;
    state.payload.priority_level = null;
    state.payload.description = null;
    state.payload.due_date = null;
    state.payload.tags = [];
    state.payload.attachment = [];
}

const handlePage = (code) => {
    window.location.href = props.data.processPageUrl+"/"+code;
}

// const searchTitle = ({ action, searchQuery, callback }) => {
//     if (action == 'ASYNC_SEARCH') {
//             axios.post(props.data.searchTitleUrl, {
//                 'keyword' : searchQuery
//             }).then( ({data}) => {
//             callback(null, data.data);
//         })
//     }
// }

</script>
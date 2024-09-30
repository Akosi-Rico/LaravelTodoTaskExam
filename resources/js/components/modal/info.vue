<template>
    <section id="taskmodal" v-if="openmodal" class="modal">
        <div class="pt-20 pb-10  flex justify-center ">
           <div class="bg-slate-100 xs:w-svw lg:w-1/2 mx-3  rounded-lg">
                <div class="flex justify-end items-end mx-2 py-2">
                    <button @click="closeModal()" class="text-gray-900 hover:text-gray-800 z-40 rounded-full font-extrabold">&times;</button>
                </div>
                <div class="flex flex-col justify-center items-center relative top-[-85px]">
                    <div>
                        <img :src="baseimage + '/logo.png'" class="bg-slate-200 rounded-full w-28 h-28">
                    </div>
                    <div class="mx-2 py-4">
                        <b>Task Management System</b>
                    </div>
                    <div class=" flex flex-col justify-center items-center w-full  overflow-y-auto">
                        <div class="w-9/12">
                            <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                                TITLE 
                            </label>
                            <input 
                                :class="(!state.errors['payload.title'] ? 'border-gray-300 input-field' : 'error-field input-field')"
                                type="text" 
                                placeholder="Enter your Title" 
                                v-model="details.title">
                            <span class="error-text" v-if="state.errors['payload.title']">
                                {{ state.errors['payload.title'][0] }}
                            </span>
                            <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                                DESCRIPTION 
                            </label>
                            <input 
                                :class="(!state.errors['payload.description'] ? 'border-gray-300 input-field' : 'error-field input-field')" 
                                type="text" 
                                placeholder="Enter Description" 
                                v-model="details.description">
                            <span class="error-text" v-if="state.errors['payload.description']">
                                {{ state.errors['payload.description'][0] }}
                            </span>
                            <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                                STATUS 
                            </label>
                            <treeselect 
                                :class="(!state.errors['payload.status'] ? '' : 'treeselect-invalid')"  
                                :multiple="false" 
                                :options="data.statusOption" 
                                :normalizer="state.normalizer" 
                                placeholder="Please select Status" 
                                v-model="details.status"/>
                            <span class="error-text" v-if="state.errors['payload.status']">
                                {{ state.errors['payload.status'][0] }}
                            </span>
                            <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                                PRIORITY LEVEL 
                            </label>
                            <treeselect 
                                :class="(!state.errors['payload.priority_level'] ? '' : 'treeselect-invalid')"  
                                :multiple="false" 
                                :options="data.priorityOption" 
                                :normalizer="state.normalizer" 
                                placeholder="Please select Priority Level" 
                                v-model="details.priority_level"/>
                            <span class="error-text" v-if="state.errors['payload.priority_level']">
                                {{ state.errors['payload.priority_level'][0] }}
                            </span>
                            <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                                DUE DATE 
                            </label>
                            <input 
                                :class="'input-field'" 
                                :lang="lang" 
                                type="date" 
                                placeholder="Enter Due Date"
                                valueType="format"
                                v-model="details.due_date">
                            <div class="py-4">
                                <dropzone 
                                :data="data"
                                :attachment="details.attachment"
                                @uploaded="handleAttachment"/>
                            </div>
                            <button type="button" class="button-primary" @click="submit()">
                                Submit
                            </button>
                         </div>
                    </div>
                </div>
           </div>
        </div>
    </section>
 </template>
 <script setup>
 import Treeselect from 'vue3-treeselect';
 import 'vue3-treeselect/dist/vue3-treeselect.css';
 import dropzone from "../dropzone-area.vue";
 import { reactive } from "vue";
 const props = defineProps(['baseimage', 'data', 'openmodal', 'details', 'isClosed']);
 const emit = defineEmits(['isClosed'])
 const state = reactive({
    errors: [],
    isOpenModal: false,
    label: null,
    options: {
        format: 'YYYY-MM-DD',
        useCurrent: false,
    },
    normalizer(node) {
        return {
            id: node.code,
            label: node.label,
            children: node.subOptions,
        }
    },
    lang: {
        days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        placeholder: {
            date: 'Select Date',
            dateRange: 'Select Date'
        },
    },
});

const closeModal = () => {
    state.errors = [];
    emit("isClosed", true);
}

const submit = () => {
    let route = (props.details.id ? props.data.taskUpdateUrl+"/"+props.details.id : props.data.taskStoreUrl);
    let method = (props.details.id ? "PUT" : "POST");
    axios({
        method: method,
        url: route,
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
        //location.reload(); 
    });  
}

const handleAttachment = (data) => {
    props.details.attachment = data;
}
</script>
 
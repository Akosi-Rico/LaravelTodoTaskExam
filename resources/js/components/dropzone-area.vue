<template>
  <div>
    <div v-if="state.files.length > 0" class="files">
      <div class="file-item" v-for="(file, index) in state.files" :key="index" >
        <span>{{ file.name }}</span>
        <span class="delete-file" @click="handleClickDeleteFile(index)">
          Delete
        </span>
      </div>
    </div>
    <div v-else class="dropzone" v-bind="getRootProps()" >
      <div class="border-drop" :class="{ isDragActive,}">
        <input v-bind="getInputProps()" :maxFiles="5"  :multiple="1" :accept="'image/jpeg, image/png, image/jpg, image/svg, .doc, .docx, .csv, .txt, video/*'" :maxSize="4" />
        <p v-if="isDragActive" >Drop the file here ...</p>
        <p v-else>Drag and drop file here, or Click to select file</p>
      </div>
    </div>
  </div>
</template>
<script setup>
import { reactive, watch, defineProps, onMounted, defineEmits } from 'vue';
import { useDropzone } from 'vue3-dropzone';

const props = defineProps([
  'data',
  'attachment'
]);

const state = reactive({
  files: [],
});

const emit = defineEmits(['uploaded']);

const { getRootProps, getInputProps, isDragActive, ...rest } = useDropzone({
  onDrop,
});

const saveFile = (files) => {
  console.log(files);
  const formData = new FormData();
  for (var x = 0; x < files.length; x++) {
    formData.append("file[]", files[x]);
  }
  axios.post(props.data.uploadUrl, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((response) => {
      emit("uploaded", response.data.message);
    })
    .catch((err) => {
      console.error(err);
    });
};

onMounted(() => {
  state.files = props.attachment;
});


watch(state, () => {
 console.log('state', state);
});

watch(isDragActive, (s) => {
  console.log('isDragActive', isDragActive.value, rest);
});

function onDrop(acceptFiles, rejectReasons) {
  saveFile(acceptFiles);
  state.files = acceptFiles;
}

function handleClickDeleteFile(index) {
  state.files.splice(index, 1);
}
</script>
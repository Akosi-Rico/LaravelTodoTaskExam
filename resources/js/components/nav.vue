<template>
    <section class="w-dvw">
        <div class="container">
            <div class="bg-gradient-to-r from-[#482a59] to-[#1c1936] w-screen px-1 py-3"></div>
            <nav class="md:flex md:items-center p-5 md:justify-between fixed w-dvw bg-[#162131] z-10 text-slate-50  border-b-2 border-[#1c283c]">
                <div class="flex flex-row  justify-center w-1/3">
                    <div>
                        <img :src="baseimage+'/'+'/logo.png'" aria-placeholder="Logo" class="w-32 h-32"/>
                    </div>
                    <div class="px-1 pt-12">
                        <b class="text-sm">Task Management System</b>
                    </div>
                </div>
                <div>
                    <div class="grid grid-rows-3">
                        <div>
                            <b class="text-sm font-sans">Today: {{ data.currentDate }}</b>
                        </div>
                        <div>
                            <b class="text-sm font-sans">Good Day! Welcome {{ data.currentUser }}</b>
                        </div>
                        <div>
                            <button type="button" class="button-primary" @click="logout()">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </section>
</template>
<script setup>
const props = defineProps(["baseimage", "data"]);
const logout = () => {
    axios({
        method: "POST",
        url: props.data.logoutUrl,
    }).then(function (response) {
        setInterval(() => {
            location.href = props.data.loginUrl;
        }, 1000);
    })
    .catch(function (errorHandler) {
        if (errorHandler.response.status == 422) {
            state.errors = errorHandler.response.data.errors;
        }
    });
}
</script>
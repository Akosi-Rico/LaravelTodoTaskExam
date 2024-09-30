<template>
    <section class="w-dvw py-20 flex justify-center items-center">
        <div class="bg-slate-100 xs:w-3/6  sm:w-2/4 md:w-1/3  shadow-md drop-shadow-md">
            <div class="flex justify-center items-center relative top-[-50px]">
                <img :src="baseimage + '/logo.png'" class="bg-slate-200 rounded-full w-28 h-28">
            </div>
            <div class="flex justify-center items-center">
                <b>Task Management System</b>
            </div>
            <form class="shadow-lg rounded-lg p-8 w-full">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                        Name 
                    </label>
                    <input :class="(!errors['payload.name'] ? 'border-gray-300' : 'error-field')" class="input-field" id="name" name="name" type="text" placeholder="Enter your Name" v-model="payload.name">
                    <span class="error-text" v-if="errors['payload.name']">
                        {{ errors['payload.name'][0] }}
                     </span>

                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="email">
                        Email
                    </label>
                    <input :class="(!errors['payload.email'] ? 'border-gray-300' : 'error-field')"  class="input-field" id="email" name="email" type="email" placeholder="Enter your Email" v-model="payload.email">
                    <span class="error-text" v-if="errors['payload.email']">
                        {{ errors['payload.email'][0] }}
                     </span>

                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="password">
                        Password
                    </label>
                    <input :class="(!errors['payload.password'] ? 'border-gray-300' : 'error-field')"  class="input-field" id="password" name="password" type="password" placeholder="Enter your Password" v-model="payload.password">
                     <span class="error-text" v-if="errors['payload.password']">
                            {{ errors['payload.password'][0] }}
                     </span>
                     <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input :class="(!errors['payload.password'] ? 'border-gray-300' : 'error-field')" class="input-field" id="password_confirmation" name="password_confirmation" type="password" 
                     placeholder="Confirm Password" v-model="payload.password_confirmation">
                     <span class="error-text" v-if="errors['payload.password']">
                            {{ errors['payload.password'][0] }}
                     </span>
                </div>
                <div class="flex justify-center items-center p-2">
                    <span class="error-text" v-if="accountError">
                        {{ accountError }}
                    </span>
                </div>
                <button type="button" @click="submit()" class="button-primary">
                    Register
                </button>
                <button type="button" @click="login()" class="button-warning">
                    Back To Login
                </button>
            </form>
        </div>
    </section>
</template>
<script>
export default  {
    props: [
        "baseimage",
        "registerurl",
        "baseurl"
    ],
    data() {
        return {
            payload: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
            },
            errors: [],
            accountError: null,
        }
    },
    methods: {
        submit() {
            let _this = this;
            axios({
                method: 'POST',
                url: _this.registerurl,
                data: {
                    payload: _this.payload
                },
            }).then(function (response) {
                if (response.status == 200) {
                    location.href = _this.baseurl; 
                }
            })
            .catch(function (errorHandler) {
                if (errorHandler.response.status == 422) {
                    _this.accountError = null;
                    _this.errors = errorHandler.response.data.errors;
                } else if (errorHandler.response.status == 400) {
                    _this.errors = [];
                    _this.accountError = errorHandler.response.data.message;
                }
            });
        },
        login() {
            location.href = this.baseurl;
        },
    }
}
</script>

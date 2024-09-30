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
                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="email">
                        Email
                    </label>
                    <input  :class="(!errors['payload.email'] ? 'border-gray-300' : 'error-field')" class="input-field" id="email" type="text" placeholder="Enter your Email" v-model="payload.email" required>
                    <span class="error-text" v-if="errors['payload.email']">
                            {{ errors['payload.email'][0] }}
                     </span>
                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="password">
                        Password
                    </label>
                    <input :class="(!errors['payload.password'] ? 'border-gray-300' : 'error-field')" class="input-field" id="password" type="password" placeholder="Enter your Password" v-model="payload.password" required>
                    <span class="error-text" v-if="errors['payload.password']">
                            {{ errors['payload.password'][0] }}
                     </span>
                </div>
                <div class="flex justify-center items-center p-2">
                    <span class="error-text" v-if="accountError">
                        {{ accountError }}
                    </span>
                </div>
                <button type="button" @click="login()"  class="button-primary">
                    Login
                </button>
                <button type="button" @click="register()"  class="button-danger">
                    Register
                </button>
            </form>
        </div>
    </section>
</template>
<script>
export default  {
    props: [
        "baseimage",
        "loginurl",
        "baseurl",
        "registerurl",
    ],
    data() {
        return {
            payload: {
                email: null,
                password: null,
            },
            errors: [],
            accountError: null,
        }
    },
    methods: {
        login() {
            let _this = this;
            axios({
                method: 'POST',
                url: _this.loginurl,
                data: {
                    payload: _this.payload,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
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
        register() {
            location.href = this.registerurl;
        },
        loginAsGuest() {
            this.payload.email = "guest@example.com";
            this.payload.password = "testing";
            this.login();
        }
    }
}
</script>

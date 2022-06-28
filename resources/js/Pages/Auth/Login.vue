<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import {
    UserIcon,
    LockClosedIcon,
    EyeIcon,
    EyeOffIcon,
} from "@heroicons/vue/outline";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

const passwordVisible = ref(false);

const switchVisibility = () => {
    passwordVisible.value = !passwordVisible.value;
};
</script>

<template>
    <Head title="Log in" />

    <JetAuthenticationCard>
        <div class="md:flex mt-10 mb-10">
            <div class="hidden md:block w-3/4 text-center">
                <div class="content-center justify-center items-center">
                    <div class="flex flex-col justify-center items-center">
                        <img
                            src="../../../assets/images/impark_logo.png"
                            class="h-14 w-auto"
                            alt=""
                            srcset=""
                        />
                        <h1 class="text-2xl text-gray-600 font-regular -mt-2">
                            Welcome Back!
                        </h1>
                    </div>
                    <div class="img w-full">
                        <img
                            class="w-72 mx-auto"
                            src="../../../assets/images/login-side-bg.png"
                            alt=""
                            srcset=""
                        />
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2 mt-3 md:px-10">
                <div class="mb-10">
                    <h1 class="font-light text-2xl text-gray-600">Hello!</h1>
                    <p class="text-sm font-medium mb-4 mt-2 text-gray-600">
                        Sign in to your account to continue.
                    </p>
                </div>
                <JetValidationErrors class="mb-4" />
                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-5">
                    <div>
                        <JetLabel for="email" value="Email" />
                        <div class="flex mt-2">
                            <div
                                class="w-10 z-10 pl-1 text-center pointer-events-none flex items-center justify-center"
                            >
                                <UserIcon
                                    class="text-gray-500 w-5 h-5"
                                ></UserIcon>
                            </div>
                            <JetInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="-ml-10 pl-10 pr-3 py-2 rounded-2xl border-2xl w-full"
                                required
                                autofocus
                                placeholder="example@mail.com"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <JetLabel for="password" value="Password" />
                        <div class="flex items-center mt-2">
                            <div
                                class="text-sm w-10 z-10 pl-1 text-center pointer-events-none flex items-center justify-center"
                            >
                                <lock-closed-icon
                                    class="text-gray-500 w-6"
                                ></lock-closed-icon>
                            </div>
                            <JetInput
                                id="password"
                                v-model="form.password"
                                :type="passwordVisible ? 'text' : 'password'"
                                class="w-full -ml-10 pl-10 pr-3 py-2 rounded-2xl border-2xl"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <div
                                @click="switchVisibility"
                                class="text-gray-600 cursor-pointer w-5 h-5 -ml-7"
                            >
                                <eye-icon v-if="passwordVisible"></eye-icon>
                                <eye-off-icon v-else></eye-off-icon>
                            </div>
                        </div>
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <JetCheckbox
                                v-model:checked="form.remember"
                                name="remember"
                            />
                            <span class="ml-2 text-sm text-gray-600"
                                >Remember me</span
                            >
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <!-- <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="underline text-sm text-gray-600 hover:text-gray-900"
                        >
                            Forgot your password?
                        </Link> -->

                        <JetButton
                            class="ml-4 mt-10"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Sign In
                        </JetButton>
                    </div>
                </form>
            </div>
        </div>
    </JetAuthenticationCard>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted, reactive } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";

const users = ref([]);
const form = reactive({
    name: "",
    email: "",
    password: "",
});

const getUsers = () => {
    axios.get("/api/users").then((response) => {
        users.value = response.data;
    });
};

const createUser = (values ,{ resetForm }) => {
    axios.post("/api/users", values).then((response) => {
        users.value.unshift(response.data);
        $("#CreateUserModal").modal("hide");
        resetForm();
    });
};

const schema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(6),
});

// const createUser = () => {
//     axios.post("/api/users", form).then((response) => {
//         users.value.unshift(response.data);
//         form.name = "";
//         form.email = "";
//         form.password = "";
//         $("#CreateUserModal").modal("hide");
//     });
// };

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button
                        type="button"
                        class="mb-2 btn btn-primary"
                        data-toggle="modal"
                        data-target="#CreateUserModal"
                    >
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New User
                    </button>
                    <div>
                        <button type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected users</span>
                    </div>
                </div>
                <div>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search..."
                    />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" />
                                </th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td>
                                    <input type="checkbox" />
                                </td>
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>2021-09-09</td>
                                <td>Admin</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!----------Add Modal--------->
    <div
        class="modal fade"
        id="CreateUserModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User</h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <Form
                    @submit="createUser"
                    :validation-schema="schema"
                    v-slot="{ errors }"
                >
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field
                                :class="{ 'is-invalid': errors.name }"
                                type="text"
                                class="form-control"
                                name="name"
                                id="name"
                                placeholder="Enter full name"
                            />
                            <span class="invalid-feedback">{{
                                errors.name
                            }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field
                                :class="{ 'is-invalid': errors.email }"
                                name="email"
                                id="email"
                                type="email"
                                class="form-control"
                                placeholder="Enter email"
                            />
                            <span class="invalid-feedback">{{
                                errors.email
                            }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <Field
                                :class="{ 'is-invalid': errors.password }"
                                name="password"
                                type="password"
                                class="form-control"
                                placeholder="Enter password"
                            />
                            <span class="invalid-feedback">{{
                                errors.password
                            }}</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

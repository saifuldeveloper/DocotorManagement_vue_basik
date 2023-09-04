<script setup>
import axios from "axios";
import { ref, onMounted, watch} from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { UseToastr } from "../../toastr.js";
import UserListItem from './UserListItem.vue';
import { debounce } from "lodash";



const toastr = UseToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);


const getUsers = () => {
    axios.get("/api/users").then((response) => {
        users.value = response.data;
    });
};

const addUser = () => {
    editing.value = false;
    $("#UserFormModal").modal("show");
};

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
});

const createUser = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/users", values)
        .then((response) => {
            users.value.unshift(response.data);
            $("#UserFormModal").modal("hide");
            resetForm();
            toastr.success("User Crteate Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};
const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $("#UserFormModal").modal("show");
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = (values, { setErrors }) => {
    axios
        .put("/api/users/" + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(
                (user) => user.id === response.data.id
            );
            users.value[index] = response.data;
            $("#UserFormModal").modal("hide");
            toastr.success("User update Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
};

const userDeleted=(userId)=>{
    users.value = users.value.filter((user => user.id !== userId));
    
}


const searchQuery = ref("");

const search = () => {
    axios.get('/api/users/search',{
        params:{
            query:searchQuery.value
        }
    })
    .then((response) => {
        users.value = response.data;
    })
    .catch((error) => {
        console.log(error);
    });
};



watch(searchQuery,debounce(()=>{
    search();
},300));
 








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
                        @click="addUser"
                        type="button"
                        class="mb-2 btn btn-primary"
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
                         v-model="searchQuery"
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
                        <tbody  v-if="users.length > 0">
                           <UserListItem  v-for="(user, index) in users" 
                               :key="user.id"
                                 :user="user"
                                    :index=index
                                    @edit-user="editUser"
                                    @user-deleted="userDeleted"

                           />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No results found...</td>
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
        id="UserFormModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
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
                    ref="form"
                    @submit="handleSubmit"
                    :validation-schema="
                        editing ? editUserSchema : createUserSchema
                    "
                    v-slot="{ errors }"
                    :initial-values="formValues"
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

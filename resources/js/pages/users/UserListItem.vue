<script setup>
import {formatDate} from "../../helper.js"
import { ref, } from "vue";
import { UseToastr } from "../../toastr.js";





const toastr = UseToastr();
  defineProps ({
    user: Object,
    index: Number,  

  });
  const emit =defineEmits(['userDeleted' , 'editUser']);
  const UserIdBeignDeleted =ref(null);


  const confrimUserDeletation = (user) => {
    UserIdBeignDeleted.value = user.id;
    $("#deleteUserModal").modal("show");
};

const deleteUser =()=>{
    axios.delete(`/api/users/${UserIdBeignDeleted.value}`)
    .then((response)=>{
        $("#deleteUserModal").modal("hide");
        toastr.success("User delete Successfully");
        emit("userDeleted",UserIdBeignDeleted.value);
    })
}


const editUser = (user) => {
    emit("editUser",user);
};


</script>



<template>
    <tr>
        <td>
            <input type="checkbox" />
        </td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at) }}</td>
        <td>Admin</td>
        <td>
            <a href="#" @click.prevent="editUser(user)">
                <i class="fa fa-edit"></i
            ></a>
            <a
                href="#" @click.prevent="confrimUserDeletation(user)"
            >
                <i
                    class="fa fa-trash text-danger ml-2"
                ></i
            ></a>
        </td>
    </tr>



    <div
    class="modal fade"
    id="deleteUserModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <span>Delete User</span>
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
            <div class="modal-body">
                <h5>Are you sure you want to delete this user ?</h5>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Cancel
                </button>
                <button
                    @click.prevent="deleteUser"
                    type="button"
                    class="btn btn-primary"
                >
                    Delete User
                </button>
            </div>
        </div>
    </div>
</div>
</template>
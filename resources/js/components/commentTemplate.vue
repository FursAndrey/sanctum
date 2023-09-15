<template>
    <div :class="[this.comment_id === '0'? '' : 'ms-16']">
        <h2 class="text-2xl font-bold text-center cursor-pointer" @click="getCommentsForPost()"><slot></slot></h2>
        <div class="w-32 p-3 me-6 rounded-lg bg-sky-500 text-white hover:bg-sky-700 font-semibold" @click="toggleComment()">
            <span v-if="this.comment_id === '0'">Add comment</span>
            <span v-else>Add answer</span>
        </div>
        <div v-if="isAuth() && isShow" class="mx-auto w-3/5">
            <textarea v-model="comment" placeholder="body of comment" class="w-full h-32 p-3 border-2 rounded-lg border-sky-500"></textarea>
            <input @click="createComment()" type="button" value="Create comment" class="block w-48 p-3 mb-2 rounded-lg bg-sky-500 text-white hover:bg-sky-700 font-semibold"/>
        </div>
            {{ errorMessage }}
        <div v-for="comment in comments" :key="comment.id" class="border-t border-sky-500 mt-2 pt-2">
            <div class="text-sm text-left text-slate-400">{{ comment.user }}</div>
            <div class="text-sm text-right text-slate-400">{{ comment.published }}</div>
            <div v-if="!isEdit">{{ comment.body }}</div>
            <textarea 
                v-if="(isOwner(comment.user) || isAdmin()) && (isEdit && idEdit == comment.id)" 
                v-model="comment.body" 
                placeholder="body of comment" 
                class="w-full h-32 p-3 border-2 rounded-lg border-sky-500"
                ></textarea>
            <div class="flex">
                <div 
                    v-if="(isOwner(comment.user) || isAdmin()) && (isEdit && idEdit == comment.id)"
                    class="w-32 p-3 me-6 font-bold bg-green-700 hover:bg-green-900 text-white rounded-lg text-center cursor-pointer"
                    @click="changeComment(comment)">
                    Save
                </div>
                <div 
                    v-if="isAdmin() && (isEdit && idEdit == comment.id)"
                    class="w-32 p-3 me-6 font-bold bg-orange-500 hover:bg-orange-700 text-white rounded-lg text-center cursor-pointer"
                    @click="toggleEditComment()">
                    Cancel
                </div>
                <div 
                    v-if="(isOwner(comment.user) || isAdmin()) && !isEdit"
                    class="w-32 p-3 me-6 font-bold bg-orange-500 hover:bg-orange-700 text-white rounded-lg text-center cursor-pointer"
                    @click="editComment(comment)">
                    Edit
                </div>
                <div 
                    v-if="isAdmin() && !isEdit"
                    class="w-32 p-3 me-6 font-bold bg-red-700 hover:bg-red-900 text-white rounded-lg text-center cursor-pointer"
                    @click="deletedComment(comment.id)">
                    Delete
                </div>
            </div>
            <comment-template 
                v-bind:post_id="String(this.post_id)" 
                v-bind:comment_id="String(comment.id)" 
                @createdNewComment="createdComment(comment.id)"
                @destroyOneComment="destroyedComment(comment.id)"
                >
                Answers {{ comment.answerCount }} <span v-if="comment.answerCount != 0">(click for open)</span>
            </comment-template>
        </div>
    </div>
</template>

<script>
import useComments from '../composition/comments';
import useInspector from '../composition/inspector';
import { ref } from "vue";
export default {
    name: 'commentTemplate',
    props: {
        post_id: {
            type: String,
            required:false
        },
        comment_id: {
            type: String,
            required:false
        },
    },
    
    setup(props, {emit}) {
        const isShow = ref(false);
        const isEdit = ref(false);
        const idEdit = ref(0);

        const { isAuth, isAdmin, isOwner } = useInspector();
        const { comment, comments, errorMessage, getComments, storeComment, updateComment, destroyComment } = useComments();

        // const qqq = () => {
        //     console.log(props.post_id);
        // }

        const getCommentsForPost = () => {
            getComments(props.post_id, props.comment_id)
        }
        
        const createComment = () => {
            storeComment(props.post_id, comment, props.comment_id);
            if (errorMessage.value == '') {
                toggleComment();
                comment.value = '';
                emit('createdNewComment');
                getComments(props.post_id);
            }
        }

        const toggleComment = () => {
            isShow.value = !isShow.value;
        }

        const toggleEditComment = () => {
            isEdit.value = !isEdit.value;
        }

        const deletedComment = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            destroyComment(id);
            emit('destroyOneComment');
            getComments(props.post_id);
        }

        const changeComment = (comment) => {
            updateComment(comment);
            toggleEditComment();
        }

        const createdComment = async (id) => {
            for (let index = 0; index < comments.value.length; index++) {
                if (comments.value[index].id == id) {
                    emit('createdNewComment');
                    comments.value[index].answerCount++;
                }
            }
        }

        const destroyedComment = (id) => {
            for (let index = 0; index < comments.value.length; index++) {
                if (comments.value[index].id == id) {
                    emit('destroyOneComment');
                    --comments.value[index].answerCount;
                }
            }
        }

        const editComment = (comment) => {
            if (isOwner(comment.user) === true || isAdmin() === true) {
                toggleEditComment();
                idEdit.value = comment.id;
            }
        }
        
        return {
            comment,
            comments,
            errorMessage,
            isShow,
            isEdit,
            idEdit,
            toggleComment,
            toggleEditComment,
            isAuth,
            isAdmin,
            isOwner,
            getCommentsForPost,
            createComment,
            createdComment,
            changeComment,
            deletedComment,
            destroyedComment,
            editComment
        }
    },
}
</script>

<style>

</style>
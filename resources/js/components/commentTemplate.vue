<template>
    <div :class="[this.comment_id === '0'? '' : 'ms-16']">
        <h2 class="text-2xl font-bold text-center cursor-pointer" @click="getCommentsForPost()"><slot></slot></h2>
        <div class="block w-48 p-3 mb-2 rounded-lg bg-sky-500 text-white hover:bg-sky-700 font-semibold" @click="toggleComment()">
            <span v-if="this.comment_id === '0'">Add comment</span>
            <span v-else>Add answer</span>
        </div>
        <div v-if="isAuth() && isShow" class="mx-auto w-3/5">
            <textarea v-model="comment" placeholder="body of comment" class="w-full h-32 p-3 border-2 rounded-lg border-sky-500"></textarea>
            <input @click="createComment()" type="button" value="Create comment" class="block w-48 p-3 mb-2 rounded-lg bg-sky-500 text-white hover:bg-sky-700 font-semibold"/>
        </div>
        <div v-for="comment in comments" :key="comment.id" class="border-t border-sky-500 mt-2 pt-2">
            <div class="text-sm text-left text-slate-400">{{ comment.user }}</div>
            <div class="text-sm text-right text-slate-400">{{ comment.published }}</div>
            <div>{{ comment.body }}</div>
            <comment-template 
                v-bind:post_id="String(this.post_id)" 
                v-bind:comment_id="String(comment.id)" 
                @createdNewComment="createdComment(comment.id)">
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

        const { isAuth } = useInspector();
        const { comment, comments, errorMessage, getComments, storeComment } = useComments();

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

        const createdComment = async (id) => {
            for (let index = 0; index < comments.value.length; index++) {
                if (comments.value[index].id == id) {
                    comments.value[index].answerCount++;
                }
            }
        }
        
        return {
            comment,
            comments,
            isShow,
            toggleComment,
            isAuth,
            getCommentsForPost,
            createComment,
            createdComment
        }
    },
}
</script>

<style>

</style>
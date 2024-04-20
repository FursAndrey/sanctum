<template>
    <div 
        v-if="(is_liked !== isLiked || like_count !== likesCount) && isToggled === false" 
        @click="initAfterMount()"
        class="inline-block cursor-pointer p-3 mb-3 text-center rounded-lg bg-indigo-800"
        >
        Показать лайки
    </div>
    <div v-else class="flex ml-4">
        <svg @click="likeToggle()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
            :class="['stroke-indigo-800 cursor-pointer hover:fill-indigo-800 w-6 h-6', isLiked ? 'fill-indigo-800': 'fill-white']">
            <path stroke-linecap="round" stroke-linejoin="round" 
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" 
            />
        </svg>
        <span class="ml-2">{{ likesCount }}</span>
    </div>
</template>

<script>
import useLikes from '../composition/likes';
import useInspector from '../composition/inspector';
import { ref, onMounted } from "vue";
export default {
    name: 'postLikeTemplate',
    
    props: {
        is_liked: {
            type: Boolean,
            required: true,
            default: false
        },
        like_count: {
            type: Number,
            required: true,
            default: 0
        },
        post_id: {
            type: String,
            required: true
        },
    },
    
    setup(props) {
        const isLiked = ref(false);
        const isToggled = ref(false);
        const likesCount = ref(0);
        const { like, togglePostLike } = useLikes();
        const { isAuth } = useInspector();

        const likeToggle = async () => {
            if (isAuth() === true) {
                await togglePostLike(props.post_id);

                isLiked.value = like.value.is_liked;
                likesCount.value = like.value.likes_count;
                isToggled.value = true;
            }
        }

        const isLikedInit = () => {
            if (props.is_liked === undefined) {
                isLiked.value = false;
            } else {
                isLiked.value = props.is_liked;
            }
        }
        const likesCountInit = () => {
            likesCount.value = props.like_count;
        }

        const initAfterMount = () => {
            isLikedInit();
            likesCountInit();
        }

        onMounted(isLikedInit);
        onMounted(likesCountInit);

        return {
            isLiked,
            likesCount,
            isToggled,
            likeToggle,
            initAfterMount
        }
    }
}
</script>

<style>

</style>
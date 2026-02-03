<script setup lang="ts">
import { computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

interface Props {
    content: string;
}

const props = defineProps<Props>();

// Настройка marked для безопасного HTML
marked.setOptions({
    breaks: true, // Поддержка переносов строк
    gfm: true, // GitHub Flavored Markdown
});

const renderedContent = computed(() => {
    const html = marked(props.content);
    // Санитизация HTML для безопасности
    return DOMPurify.sanitize(html as string);
});
</script>

<template>
    <div 
        class="prose max-w-none text-gray-700 dark:text-white"
        v-html="renderedContent"
    />
</template>

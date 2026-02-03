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
        class="prose prose-sm max-w-none prose-invert text-white prose-p:text-white prose-headings:text-white prose-strong:text-primary-orange prose-ul:text-white prose-li:text-white prose-ol:text-white [&_li]:text-white [&_p]:text-white prose-a:text-primary-orange hover:prose-a:text-white"
        v-html="renderedContent"
    />
</template>

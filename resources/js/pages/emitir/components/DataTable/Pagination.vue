<script setup>
// import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button' // Asumiendo que usas shadcn-vue
// (Quitamos los iconos que no se usaban para limpiar)

defineProps({
    links: Array,
    from: Number,
    to: Number,
    total: Number
})
</script>

<template>
    <div class="flex items-center justify-between py-3">
        <div class="text-sm text-muted-foreground">
            Mostrando
            <span class="font-medium">{{ from }}</span>
            a
            <span class="font-medium">{{ to }}</span>
            de
            <span class="font-medium">{{ total }}</span>
            resultados
        </div>

        <div v-if="links.length > 3" class="flex items-center space-x-1">
            <template v-for="(link, key) in links" :key="key">
                <Button v-if="link.url === null" variant="outline" size="sm" :disabled="true" class="opacity-50">
                    <span v-html="link.label" />
                </Button>

                <Button v-else-if="link.active" as="Link" :href="link.url" variant="default" size="sm" preserve-scroll
                    preserve-state>
                    <span v-html="link.label" />
                </Button>

                <Button v-else as="Link" :href="link.url" variant="outline" size="sm" preserve-scroll preserve-state>
                    <span v-html="link.label" />
                </Button>
            </template>
        </div>
    </div>
</template>

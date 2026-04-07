<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <!-- <pre>{{ $page.props.user }}</pre> -->
    <div>
        <button
            v-if="$page.props.user.permissions.some(p => p.trim() === 'create role')">
            Create Role
        </button>

        <button
            v-if="$page.props.user.permissions.some(p => p.trim() === 'create lesson')">
            Create New Lesson
        </button>
    </div>
</template>

<style>
    button {
        background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }
</style>

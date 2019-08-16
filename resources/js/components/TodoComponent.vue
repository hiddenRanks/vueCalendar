<template>
    <div>
        <div class="row">
            <div class="col">
                <h2>Todo List</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition-group name="glist" tag="div">
                    <item-compo class="my-3" v-for="item in uncomList" :key="item.id" :item="item"></item-compo>
                </transition-group>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TodoComponent",
        computed: {
            uncomList() {
                return this.$root.sortList.filter(x => x.complete == 0);
            }
        }
    }
</script>

<style scoped>

</style>

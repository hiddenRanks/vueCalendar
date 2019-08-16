<template>
    <div>
        <div class="row">
            <div class="col">
                <h2>Complete List</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition-group name="glist" tag="div">
                    <item-compo class="my-3" v-for="item in comList" :key="item.id" :item="item"></item-compo>
                </transition-group>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CompleteComponent",
        computed: {
            comList() {
                return this.$root.sortList.filter(x => x.complete == 1);
            }
        }
    }
</script>

<style scoped>

</style>

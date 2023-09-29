import {reactive} from "vue";

export default reactive({
    items: [],
    add(toast) {
        this.items.unshift({
            key: Symbol(),
            ...toast
        });
    },
    remove(key) {
        this.items = this.items.filter(item => item.key !== key);
    }

})

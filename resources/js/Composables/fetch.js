import { ref } from 'vue'

export function useFetch(url) {
    return new Promise(async (resolve, reject) => {
        try {
            const res = await fetch(url);
            if (!res.ok) {
                throw new Error("Network response was not ok");
            }
            const data = await res.json();
            resolve(data);
        } catch (err) {
            reject(err);
        }
    });

    // const data = ref(null)
    // const error = ref(null)
    //
    // fetch(url)
    //     .then((res) => res.json())
    //     .then((json) => (data.value = json))
    //     .catch((err) => (error.value = err))
    //
    // return { data, error }
}

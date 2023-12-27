<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {onMounted} from 'vue';
import DisplayStage from '@/Atoms/DisplayStage.vue';
import Button from '@/Atoms/Button.vue';
import PackageIndicator from '@/Atoms/PackageIndicator.vue';
import CategorySection from '@/Organisms/CategorySection.vue';

const props = defineProps({
    deal: {
        type: Object,
    },
    dealCategories: {
        type: Array,
    },
});

onMounted(() => {
    console.log(props.deal);
});

</script>

<template>
    <Head title="Deal Detail"/>

    <AuthenticatedLayout>


        <div class="py-6 mt-10">
            <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">
                <div class="flex justify-between">
                    <h2 class="text-center mb-5 text-lg md:text-left md:text-xl">Deal #{{ deal.bitrix_deal_id }}</h2>
                    <Link :href="route('applications')" class="text-center mb-5 text-lg md:text-left md:text-xl">
                        <Button type="primary">
                            Back
                        </Button>
                    </Link>
                </div>
                <div class="grid sm:grid-cols-4 sm:grid-rows-2 gap-4 pb-5 ">
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">University</span>
                        <h5 class="text-md">{{ deal.university }}</h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Degree</span>
                        <h5 class="text-md">{{ deal.degree }}</h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Applied</span>
                        <h5 class="text-md">{{ deal.created_at }}</h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <h5 class="text-md">
                            <PackageIndicator
                                :package-id="deal.package_id"
                            />
                        </h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Program</span>
                        <h5 class="text-md">{{ deal.program }}</h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h5 class="text-md">{{ deal.intake }}</h5>
                    </div>
                    <div class="pt-4">
                        <span class="text-gray-400 font-medium text-md mt-3">Status</span>
                        <h5 class="text-md">
                            <DisplayStage
                                :stage-name="deal.stage_name"
                            />
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <CategorySection
            v-for="(category,key) in dealCategories"
            :key="key"
            :category-info="category"
        />
    </AuthenticatedLayout>
</template>

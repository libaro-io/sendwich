<template>
    <input type="checkbox" id="my-modal-6" class="modal-toggle"/>
    <div class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="font-bold text-lg">New Store</h3>
            <div class="grid grid-cols-2 justify-items-center mt-3">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name *</span>
                    </label>
                    <input type="text" required placeholder="Libaro" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <input type="text" placeholder="Vaartdijkstraat 19" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">ZIP</span>
                    </label>
                    <input type="text" placeholder="8200" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City</span>
                    </label>
                    <input type="text" placeholder="Brugge" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="text" placeholder="+32 477 11 22 33" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="text" placeholder="info@libaro.be" class="input input-bordered w-full max-w-xs"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Website</span>
                    </label>
                    <input type="text" placeholder="https://libaro.be" class="input input-bordered w-full max-w-xs"/>
                </div>
            </div>
            <div class="modal-action">
                <label for="my-modal-6" class="btn btn-success">Create</label>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NewStore"
}
</script>

<style scoped>

</style>

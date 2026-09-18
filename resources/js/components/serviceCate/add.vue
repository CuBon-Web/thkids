<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Thêm mới danh mục dịch vụ</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                    label-placeholder="Tên danh mục"
                  />
                </div>
                <div class="form-group">
                  <label>Ảnh đại diện</label>
                  <image-multi-upload v-model="objData.image" :title="'danh-muc-dich-vu'" />
                  <!-- <before-after-upload
                    v-model="compareImage"
                    :title="'danh-muc-dich-vu'"
                    :show-title="false"
                  /> -->
                </div>
                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <vs-textarea v-model="objData.description" />
                </div>
                <div class="form-group">
                    <label>Nội dung</label>
                    <TinyMce v-model="objData.content" />
                </div>
                <div class="form-group">
                  <label for="exampleInputName1">Trạng thái</label>
                  <vs-select v-model="objData.status"
                  >
                      <vs-select-item  value="1" text="Hiện" />
                      <vs-select-item  value="0" text="Ẩn" />
                    </vs-select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row fixxed">
        <div class="col-12">
          <div class="saveButton">
            <vs-button color="primary" @click="saveEdit()">Cập nhật</vs-button>
          </div>
        </div>
      </div>
    <!-- content-wrapper ends -->
  </div>
</template>

<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
export default {
  data() {
    return {
      showLang:{
        title:false
      },
      objData: {
        name: "",
        content: "",
        image: [],
        description:"",
        status: 1,
      },
      lang:[],
      img: "",
      errors:[]
    };
  },
components: {
    TinyMce,
  },
  methods: {
    ...mapActions(["saveCategoryService","listLanguage", "loadings"]),
    normalizeImagePayload(image) {
      const list = Array.isArray(image)
        ? image
        : (image ? [image] : []);
      return JSON.stringify(list.filter(url => typeof url === "string" && url.trim() !== ""));
    },
    saveEdit() {
      this.errors = [];
      this.objData.image = this.normalizeImagePayload(this.objData.image);
      if(this.objData.name == '') this.errors.push('Tên danh mục không được để trống');
      if(this.objData.description == '') this.errors.push('Mô tả ngắn không để trống');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveCategoryService(this.objData)
        .then(response => {
            this.loadings(false);
            this.$router.push({ name: "list_category_service" });
            this.$success("Thêm danh mục thành công");
            this.$route.push({ name: "list_category_service" });
          })
          .catch(error => {
            this.loadings(false);
          });
      }
    },
    listLang(){
      this.listLanguage().then(response => {
        this.loadings(false);
        this.lang  = response.data
      }).catch(error => {

      })
    },

  },
  mounted() {
    this.loadings(true);
    this.listLang();
  }
};
</script>

<style>
</style>
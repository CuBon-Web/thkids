<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Sửa danh mục dịch vụ</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              
              
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                <label>Tên</label>
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Ảnh đại diện</label>
                  <image-multi-upload v-model="objData.image" :title="'danh-muc-dich-vu'" />
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
        id:this.$route.params.id,
        name: "",
        content: "",
        image: [],
        status: "",
        description:""
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
    ...mapActions(["getInfoCateService","saveCategoryService","listLanguage", "loadings"]),
    nameImage(event) {
      this.objData.avatar = event;
    },
    showSettingLangExist(value){
        this.showLang.title = !this.showLang.title
          this.lang.forEach((value, index) => {
              if(!this.objData.name[index] && value.code != this.objData.name[0].lang_code){
                  var oj = {};
                  oj.lang_code = value.code;
                  oj.content = ''
                  this.objData.name.push(oj)
              }
          });
    },
    parseImageForUpload(image) {
      if (!image) {
        return [];
      }
      if (Array.isArray(image)) {
        return image.filter(url => typeof url === "string" && url.trim() !== "");
      }
      try {
        const parsed = JSON.parse(image);
        if (!Array.isArray(parsed)) {
          return typeof image === "string" && image ? [image] : [];
        }
        return parsed
          .map(item => {
            if (typeof item === "string") {
              return item;
            }
            if (item && typeof item === "object") {
              return item.after || item.before || "";
            }
            return "";
          })
          .filter(url => url);
      } catch (e) {
        return typeof image === "string" && image ? [image] : [];
      }
    },
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
            this.$success("Sửa danh mục thành công");
            this.$route.push({ name: "list_category_service" });
          })
          .catch(error => {
            this.loadings(false);
            // this.$error('Sửa danh mục thất bại');
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
    getInfoCates(){
      this.loadings(true);
      this.getInfoCateService(this.objData).then(response => {
        this.loadings(false);
        if(response.data == null){
          this.objData ={
            id:this.$route.params.id,
            name: "",
            content: "",
            image: [],
            status: "",
            description:"",
          }
        }else{
          this.objData = response.data;
          this.objData.image = this.parseImageForUpload(this.objData.image);
        }
      }).catch(error => {
        console.log(12);
      });
    },
    changeLanguage(data){
      this.getInfoCates();
    }
  },
  mounted() {
    this.loadings(true);
    this.getInfoCates();
    this.listLang();
  }
};
</script>

<style>
</style>
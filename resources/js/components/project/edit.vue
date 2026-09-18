<template>
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              
              <div class="form-group">
                <label>Tên tác phẩm</label>
                <vs-input
                  type="text"
                  size="default"
                  placeholder="Tên tác phẩm"
                  class="w-100"
                  v-model="objData.name"
                />
              </div>
              <div class="form-group">
                <label>Nội dung</label>
                <TinyMce
                  v-model="objData.content[0].content"
                />
                <el-button size="small" @click="showSettingLangExist('content')">Đa ngôn ngữ</el-button>
                 <div class="dropLanguage" v-if="showLang.content == true">
                    <div class="form-group" v-for="item,index in lang" :key="index">
                        <label v-if="index != 0">{{item.name}}</label>
                        <TinyMce v-if="index != 0" v-model="objData.content[index].content" />
                    </div>
                </div>
              </div>
              <div class="form-group">
                <label>Mô tả ngắn</label>
                <vs-textarea v-model="objData.description[0].content" />
              </div>
              <div class="form-group">
                <label>Danh mục tác phẩm</label>
                <vs-select
                  class="selectExample"
                  v-model="objData.project_cate_id"
                  placeholder="Chọn danh mục"
                >
                  <vs-select-item value="0" text="Không danh mục" />
                  <vs-select-item
                    :value="item.id"
                    :text="item.name"
                    v-for="(item, index) in cate"
                    :key="'pc' + index"
                  />
                </vs-select>
              </div>
              <div class="form-group">
                <label>Tác giả</label>
                <vs-input
                  type="text"
                  size="default"
                  placeholder="Tác giả"
                  class="w-100"
                  v-model="objData.location"
                />
              </div>
              <div class="form-group">
                <label>Thời gian</label>
                <vs-input
                  type="text"
                  size="default"
                  placeholder="Thời gian"
                  class="w-100"
                  v-model="objData.operate"
                />
              </div>
              <div class="form-group">
                <label>Ảnh bài viết</label>
                <image-multi-upload v-model="objData.images" :title="'du-an'" />
                <!-- <before-after-upload v-model="objData.images" /> -->
              </div>
              <div class="form-group">
                <label>Trạng thái</label>
                <vs-select v-model="objData.status">
                  <vs-select-item value="1" text="Hiện" />
                  <vs-select-item value="0" text="Ẩn" />
                </vs-select>
              </div>
              <div class="form-group">
                <label>Nổi bật</label>
                <vs-select v-model="objData.show_home">
                  <vs-select-item value="1" text="Hiển thị trang chủ" />
                  <vs-select-item value="0" text="Không hiển thị" />
                </vs-select>
              </div>
            </div>
          </div>
          
        </div>
        
      </div>
      <div class="row fixxed">
        <div class="col-12">
          <div class="saveButton">
            <vs-button color="primary" @click="addProjects">Cập nhật</vs-button>
          </div>
        </div>
      </div>
    <!-- content-wrapper ends -->
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
import { required } from "vuelidate/lib/validators";
export default {
  name: "editSolution",
  data() {
    return {
      showLang:{
        title:false,
        content:false,
        description:false
      },
      errors:[],
      cate:[],
      type_cate:[],
      objData: {
        id: this.$route.params.id,
        name: "",
        content: [
          {
            lang_code:'vi',
            content:''
          }
        ],
        description: [
          {
            lang_code:'vi',
            content:''
          }
        ],
        project_cate_id: 0,
        status: 1,
        show_home: 0,
        images: [],
        location:"",
        scale:"",
        operate:""
      },
      lang:[]
    };
  },
  components: {
    TinyMce
  },
  computed: {},
  watch: {},
  methods: {
    ...mapActions(["addProject", "loadings","detailProject","listLanguage","listProjectCate"]),
    addProjects(){
      this.errors = [];
      if(this.objData.name == '') this.errors.push('Tên không được để trống');
      if(this.objData.content[0].content == '') this.errors.push('Nội dung không được để trống');
     if(this.objData.images.length == 0) this.errors.push('Vui lòng chọn ảnh');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      }else{
        this.loadings(true);
        this.addProject(this.objData).then(response => {
          this.loadings(false);
          this.$router.push({name:'list_project'});
          this.$success('Thành công');
        }).catch(error => {
          this.loadings(false);
          this.$error('Thất bại');
        })
      }
    },
    showSettingLangExist(value,name = "content"){
      if(value == "name"){
        this.showLang.title = !this.showLang.title
          this.lang.forEach((value, index) => {
              if(!this.objData.name[index] && value.code != this.objData.name[0].lang_code){
                  var oj = {};
                  oj.lang_code = value.code;
                  oj.content = ''
                  this.objData.name.push(oj)
              }
          });
      }
      if(value == "content"){
        this.showLang.content = !this.showLang.content
          this.lang.forEach((value, index) => {
              if(!this.objData.content[index] && value.code != this.objData.content[0].lang_code){
                  var oj = {};
                  oj.lang_code = value.code;
                  oj.content = ''
                  this.objData.content.push(oj)
              }
          });
      }
      if(value == "description"){
        this.showLang.description = !this.showLang.description
          this.lang.forEach((value, index) => {
              if(!this.objData.description[index] && value.code != this.objData.description[0].lang_code){
                  var oj = {};
                  oj.lang_code = value.code;
                  oj.content = ''
                  this.objData.description.push(oj)
              }
          });
      }
      
    },
    editById() {
      this.loadings(true);
      this.detailProject({id:this.$route.params.id}).then(response => {
        this.loadings(false);
          this.objData = response.data;
          this.objData.images = JSON.parse(response.data.images);
          this.objData.content = JSON.parse(response.data.content);
          this.objData.description = JSON.parse(response.data.description);
      }).catch(error => {
        console.log(12);
      });
    },
    listLang(){
      this.listLanguage().then(response => {
        this.lang  = response.data
      }).catch(error => {

      })
    },
    changeLanguage(data){
      this.editById();
    }
  },
  mounted() {
    this.editById();
    this.listLang();
    this.listProjectCate({}).then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
  }
};
</script>
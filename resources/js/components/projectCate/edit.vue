<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Sửa danh mục dự án</h4>

            <div class="form-group">
              <label>Tên danh mục <span class="text-danger">*</span></label>
              <vs-input class="w-100" v-model="objData.name" placeholder="Tên danh mục dự án" />
            </div>

            <div class="form-group">
              <label>Mô tả ngắn</label>
              <vs-textarea v-model="objData.description" rows="3" />
            </div>

            <div class="form-group">
              <label>Ảnh</label>
              <image-multi-upload v-model="objData.image" :title="'danh-muc-du-an'" />
              <!-- <before-after-upload v-model="objData.image" /> -->
            </div>

            <div class="form-group">
              <label>Trạng thái</label>
              <vs-select v-model="objData.status">
                <vs-select-item value="1" text="Hiện" />
                <vs-select-item value="0" text="Ẩn" />
              </vs-select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="save">Cập nhật</vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
  data() {
    return {
      objData: {
        id: this.$route.params.id,
        name: '',
        description: '',
        image: [],
        status: 1,
      },
    };
  },
  methods: {
    ...mapActions(['addProjectCate', 'detailProjectCate', 'loadings']),
    loadDetail() {
      this.loadings(true);
      this.detailProjectCate({ id: this.$route.params.id }).then(response => {
        this.loadings(false);
        const d = response.data;
        if (d) {
          this.objData = {
            id: d.id,
            name: d.name || '',
            description: d.description || '',
            image: (() => {
              try { return JSON.parse(d.image) || []; } catch { return []; }
            })(),
            status: d.status != null ? String(d.status) : '1',
          };
        }
      }).catch(() => { this.loadings(false); });
    },
    save() {
      if (!this.objData.name.trim()) {
        this.$error('Tên danh mục không được để trống');
        return;
      }
      this.loadings(true);
      this.addProjectCate(this.objData).then(() => {
        this.loadings(false);
        this.$success('Cập nhật thành công');
        this.$router.push({ name: 'list_project_category' });
      }).catch(() => {
        this.loadings(false);
        this.$error('Cập nhật thất bại');
      });
    },
  },
  mounted() {
    this.loadDetail();
  },
};
</script>

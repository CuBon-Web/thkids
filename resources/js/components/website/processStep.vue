<template>
  <div>
    <h3 class="page-title">Quản lý "How We Work" (Quy trình làm việc)</h3>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <div
              class="row"
              v-for="(item, key) in objData"
              :key="'process-' + key"
              style="border:1px solid #eee;border-radius:8px;padding:12px;margin-bottom:16px"
            >
              <div class="col-md-12 mb-2 d-flex align-items-center justify-content-between">
                <strong>Bước {{ key + 1 }}</strong>
                <label
                  v-if="objData.length > 1"
                  style="cursor:pointer;margin:0;color:#e55"
                  title="Xóa bước"
                  @click="removeItem(key)"
                >
                  <vs-icon icon="clear"></vs-icon>
                </label>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label>Ảnh icon / minh họa</label>
                  <image-upload
                    type="avatar"
                    v-model="item.image"
                    :title="'process-step-' + (key + 1)"
                  ></image-upload>
                </div>
              </div>

              <div class="col-md-9">
                <div class="form-group">
                  <label>Tiêu đề <span class="text-danger">*</span></label>
                  <vs-input
                    class="w-100"
                    v-model="item.title"
                    :placeholder="'VD: ' + (key + 1 < 10 ? '0' : '') + (key + 1) + '. Tư vấn ban đầu'"
                  />
                </div>
                <div class="form-group">
                  <label>Mô tả</label>
                  <vs-textarea v-model="item.description" rows="3" />
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Thứ tự</label>
                      <vs-input type="number" v-model="item.sort" class="w-100" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Trạng thái</label>
                      <vs-select v-model="item.status">
                        <vs-select-item value="1" text="Hiện" />
                        <vs-select-item value="0" text="Ẩn" />
                      </vs-select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <vs-button color="success" type="border" @click="addItem">
              <vs-icon icon="add"></vs-icon> Thêm bước
            </vs-button>

          </div>
        </div>
      </div>
    </div>

    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="save">Lưu</vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
  name: 'processStep',
  data() {
    return {
      objData: [this.defaultItem()],
    };
  },
  methods: {
    ...mapActions(['saveProcessStep', 'listProcessStep', 'loadings']),
    defaultItem() {
      return { title: '', description: '', image: '', sort: 0, status: 1 };
    },
    addItem() {
      this.objData.push(this.defaultItem());
    },
    removeItem(index) {
      this.objData.splice(index, 1);
    },
    load() {
      this.loadings(true);
      this.listProcessStep().then(response => {
        this.loadings(false);
        const data = response.data || [];
        if (data.length) {
          this.objData = data.map(d => ({
            title:       d.title       || '',
            description: d.description || '',
            image:       d.image       || '',
            sort:        d.sort        != null ? String(d.sort) : '0',
            status:      d.status      != null ? String(d.status) : '1',
          }));
        }
      }).catch(() => { this.loadings(false); });
    },
    save() {
      const invalid = this.objData.some(item => !item.title.trim());
      if (invalid) {
        this.$error('Tiêu đề không được để trống');
        return;
      }
      this.loadings(true);
      this.saveProcessStep({ items: this.objData }).then(() => {
        this.loadings(false);
        this.$success('Lưu thành công');
      }).catch(() => {
        this.loadings(false);
        this.$error('Lưu thất bại');
      });
    },
  },
  mounted() {
    this.load();
  },
};
</script>

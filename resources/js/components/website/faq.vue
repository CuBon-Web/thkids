<template>
  <div>
    <h3 class="page-title">Quản lý FAQ (Câu hỏi thường gặp)</h3>
    <p class="text-muted mb-3">Hiển thị tại trang chủ và trang FAQ. Kéo thả thứ tự bằng số "Thứ tự".</p>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div
              v-for="(item, key) in objData"
              :key="'faq-' + key"
              class="faq-admin-item"
            >
              <div class="faq-admin-item__head">
                <strong>Câu hỏi #{{ key + 1 }}</strong>
                <label
                  v-if="objData.length > 1"
                  class="faq-admin-item__remove"
                  title="Xóa"
                  @click="removeItem(key)"
                >
                  <vs-icon icon="clear"></vs-icon>
                </label>
              </div>

              <div class="form-group">
                <label>Câu hỏi <span class="text-danger">*</span></label>
                <vs-input
                  class="w-100"
                  v-model="item.question"
                  placeholder="VD: Xăm hình có đau không?"
                />
              </div>

              <div class="form-group">
                <label>Câu trả lời</label>
                <vs-textarea v-model="item.answer" rows="4" placeholder="Nội dung trả lời..." />
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

            <vs-button color="success" type="border" @click="addItem">
              <vs-icon icon="add"></vs-icon> Thêm câu hỏi
            </vs-button>
          </div>
        </div>
      </div>
    </div>

    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="save">Lưu FAQ</vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "faqAdmin",
  data() {
    return {
      objData: [this.defaultItem()],
    };
  },
  methods: {
    ...mapActions(["saveFaq", "listFaq", "loadings"]),

    defaultItem() {
      return {
        question: "",
        answer: "",
        sort: 0,
        status: "1",
      };
    },

    addItem() {
      const next = this.defaultItem();
      next.sort = this.objData.length;
      this.objData.push(next);
    },

    removeItem(index) {
      this.objData.splice(index, 1);
    },

    load() {
      this.loadings(true);
      this.listFaq()
        .then((response) => {
          this.loadings(false);
          const data = response.data || [];
          if (data.length) {
            this.objData = data.map((d) => ({
              question: d.question || "",
              answer: d.answer || "",
              sort: d.sort != null ? String(d.sort) : "0",
              status: d.status != null ? String(d.status) : "1",
            }));
          }
        })
        .catch(() => {
          this.loadings(false);
        });
    },

    save() {
      const invalid = this.objData.some((item) => !item.question.trim());
      if (invalid) {
        this.$error("Câu hỏi không được để trống");
        return;
      }

      this.loadings(true);
      this.saveFaq({ items: this.objData })
        .then(() => {
          this.loadings(false);
          this.$success("Lưu FAQ thành công");
          this.load();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Lưu FAQ thất bại");
        });
    },
  },
  mounted() {
    this.load();
  },
};
</script>

<style scoped>
.faq-admin-item {
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  background: #fafafa;
}
.faq-admin-item__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.faq-admin-item__remove {
  cursor: pointer;
  margin: 0;
  color: #e55;
}
</style>

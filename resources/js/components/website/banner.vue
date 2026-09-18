<template>
  <div>
      <h3 class="page-title">Quản lý banner</h3>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div
                class="row banner-item-row"
                v-for="(item, key) in objData"
                :key="'banner-' + key"
              >
                <div class="col-md-12 mb-3 d-flex align-items-center justify-content-between">
                  <strong>Banner #{{ key + 1 }}</strong>
                  <label
                    v-if="key != 0"
                    style="cursor: pointer; margin: 0"
                    title="Xóa banner"
                    @click="removeObjBanner(key)"
                  >
                    <vs-icon icon="clear"></vs-icon>
                  </label>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Loại banner</label>
                    <vs-select v-model="item.type" class="w-100" @input="onTypeChange(item)">
                      <vs-select-item value="image" text="Ảnh" />
                      <vs-select-item value="youtube" text="Video YouTube" />
                    </vs-select>
                  </div>
                </div>

                <div class="col-md-3" v-if="item.type === 'image'">
                  <div class="form-group">
                    <label>Ảnh banner (Desktop)</label>
                    <p class="text-muted small mb-2">
                      Ảnh nền riêng cho giao diện desktop.
                    </p>
                    <image-upload
                      type="avatar"
                      v-model="item.image"
                      :title="'banner-trang-chu'"
                    ></image-upload>
                  </div>
                  <div class="form-group">
                    <label>Ảnh banner (Mobile)</label>
                    <p class="text-muted small mb-2">
                      Ảnh nền riêng cho giao diện điện thoại.
                    </p>
                    <image-upload
                      type="avatar"
                      v-model="item.image_mobile"
                      :title="'banner-trang-chu-mobile'"
                    ></image-upload>
                  </div>
                </div>

                <div class="col-md-3" v-else>
                  <div class="form-group">
                    <label>Ảnh nền (tùy chọn)</label>
                    <p class="text-muted small mb-2">
                      Dùng khi video chưa tải hoặc làm ảnh bìa trên mobile.
                    </p>
                    <image-upload
                      type="avatar"
                      v-model="item.image"
                      :title="'banner-youtube-poster'"
                    ></image-upload>
                  </div>
                </div>

                <div :class="item.type === 'youtube' ? 'col-md-9' : 'col-md-9'">
                  <div class="form-group" v-if="item.type === 'youtube'">
                    <label>Link / ID video YouTube <span class="text-danger">*</span></label>
                    <vs-input
                      type="text"
                      v-model="item.video_url"
                      size="default"
                      placeholder="https://www.youtube.com/watch?v=... hoặc mã video 11 ký tự"
                      class="w-100"
                    />
                    <small class="text-muted" v-if="youtubePreviewId(item)">
                      Xem trước:
                      <a
                        :href="'https://www.youtube.com/watch?v=' + youtubePreviewId(item)"
                        target="_blank"
                        rel="noopener"
                      >
                        youtube.com/watch?v={{ youtubePreviewId(item) }}
                      </a>
                    </small>
                  </div>

                  <div class="form-group">
                    <label>Tiêu đề (Bỏ trống nếu là banner nhỏ)</label>
                    <vs-input
                      type="text"
                      v-model="item.title"
                      size="default"
                      placeholder="Tiêu đề banner"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Mô tả</label>
                    <TinyMce
                      v-model="item.description"
                      size="default"
                      placeholder="Mô tả banner"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Link nút CTA</label>
                    <vs-input
                      type="text"
                      v-model="item.link"
                      size="default"
                      placeholder="Link banner"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Trạng thái</label>
                    <vs-select v-model="item.status">
                      <vs-select-item value="1" text="Hiện" />
                      <vs-select-item value="0" text="Ẩn" />
                      <vs-select-item value="2" text="Banner nhỏ" />
                    </vs-select>
                  </div>
                </div>

                <hr style="border: 0.5px solid #04040426; width: 100%; margin: 24px 0" />
              </div>
              <vs-button color="primary" @click="saveBanners">Lưu</vs-button>
              <vs-button color="success" @click="addObjBanner">Thêm banner</vs-button>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
const defaultBanner = () => ({
  image: "",
  image_mobile: "",
  type: "image",
  video_url: "",
  status: 1,
  link: "",
  title: "",
  description: "",
});

export default {
  name: "banner",
  data() {
    return {
      objData: [defaultBanner()],
    };
  },
  components: {
    TinyMce,
  },
  methods: {
    ...mapActions(["saveBanner", "loadings", "listBanner"]),
    normalizeBanner(item) {
      return {
        ...defaultBanner(),
        ...item,
        type: item.type === "youtube" ? "youtube" : "image",
        video_url: item.video_url || "",
        status: String(
          item.status !== undefined && item.status !== null ? item.status : 1
        ),
      };
    },
    youtubePreviewId(item) {
      const url = (item.video_url || "").trim();
      if (!url) return "";
      if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
      const match = url.match(
        /(?:youtube\.com\/(?:[^/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?/\s]{11})/
      );
      return match ? match[1] : "";
    },
    onTypeChange(item) {
      if (item.type === "image") {
        item.video_url = "";
      }
    },
    validateBanners() {
      for (let i = 0; i < this.objData.length; i++) {
        const item = this.objData[i];
        if (item.type === "youtube") {
          if (!this.youtubePreviewId(item)) {
            this.$error(`Banner #${i + 1}: Vui lòng nhập link YouTube hợp lệ.`);
            return false;
          }
        } else if (!item.image) {
          this.$error(`Banner #${i + 1}: Vui lòng tải ảnh banner desktop.`);
          return false;
        } else if (!item.image_mobile) {
          this.$error(`Banner #${i + 1}: Vui lòng tải ảnh banner mobile.`);
          return false;
        }
      }
      return true;
    },
    saveBanners() {
      if (!this.validateBanners()) return;

      this.loadings(true);
      this.saveBanner({ data: this.objData })
        .then(() => {
          this.loadings(false);
          this.$success("Sửa banner thành công");
          this.listBanners();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Sửa banner thất bại");
        });
    },
    addObjBanner() {
      this.objData.push(defaultBanner());
    },
    removeObjBanner(i) {
      this.objData.splice(i, 1);
    },
    listBanners() {
      this.loadings(true);
      this.listBanner()
        .then((response) => {
          this.loadings(false);
          const rows = response.data || [];
          this.objData = rows.length
            ? rows.map((row) => this.normalizeBanner(row))
            : [defaultBanner()];
        })
        .catch(() => {
          this.loadings(false);
        });
    },
  },
  mounted() {
    this.listBanners();
  },
};
</script>

<style scoped>
.banner-item-row {
  margin-bottom: 8px;
}
</style>

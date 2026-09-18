<template>
  <div class="before-after-upload">
    <div
      v-for="(pair, index) in pairs"
      :key="pair.uid"
      class="before-after-pair"
    >
      <div class="pair-toolbar">
        <strong>Cặp ảnh #{{ index + 1 }}</strong>
        <span
          v-if="pair.before && pair.after"
          class="pair-status pair-status--ok"
        >
          Đủ 2 ảnh
        </span>
        <span v-else class="pair-status pair-status--warn">Chưa đủ ảnh</span>
        <button
          v-if="!single && pairs.length > 1"
          type="button"
          class="pair-remove"
          title="Xóa cặp ảnh"
          @click="removePair(index)"
        >
          <i class="el-icon-delete"></i>
        </button>
      </div>

      <div class="form-group" v-if="showTitle">
        <label>Tiêu đề (tùy chọn)</label>
        <el-input
          v-model="pair.title"
          size="small"
          placeholder="VD: Phòng khách, Nhà bếp..."
          @input="emitInput"
        />
      </div>

      <div class="pair-upload-grid">
        <div class="pair-upload-col">
          <label class="pair-label pair-label--before">Before — Trước</label>
          <el-upload
            class="pair-uploader"
            action="'/upload'"
            name="img"
            :show-file-list="false"
            :http-request="req => uploadSide(req, pair, 'before')"
            :before-upload="beforeUpload"
          >
            <div v-if="pair.beforeUploading" class="pair-placeholder pair-placeholder--loading">
              <i class="el-icon-loading"></i>
              <span>Đang tải...</span>
            </div>
            <div v-else-if="pair.before" class="pair-preview">
              <img :src="resolveUrl(pair.before)" alt="Before" />
              <span class="pair-preview-tag">Before</span>
              <button
                type="button"
                class="pair-preview-remove"
                title="Xóa ảnh Before"
                @click.stop="clearSide(pair, 'before')"
              >
                <i class="el-icon-delete"></i>
              </button>
            </div>
            <div v-else class="pair-placeholder">
              <i class="el-icon-upload2"></i>
              <span>Tải ảnh trước</span>
            </div>
          </el-upload>
        </div>

        <div class="pair-divider" aria-hidden="true">
          <i class="el-icon-sort"></i>
        </div>

        <div class="pair-upload-col">
          <label class="pair-label pair-label--after">After — Sau</label>
          <el-upload
            class="pair-uploader"
            action="'/upload'"
            name="img"
            :show-file-list="false"
            :http-request="req => uploadSide(req, pair, 'after')"
            :before-upload="beforeUpload"
          >
            <div v-if="pair.afterUploading" class="pair-placeholder pair-placeholder--loading">
              <i class="el-icon-loading"></i>
              <span>Đang tải...</span>
            </div>
            <div v-else-if="pair.after" class="pair-preview">
              <img :src="resolveUrl(pair.after)" alt="After" />
              <span class="pair-preview-tag">After</span>
              <button
                type="button"
                class="pair-preview-remove"
                title="Xóa ảnh After"
                @click.stop="clearSide(pair, 'after')"
              >
                <i class="el-icon-delete"></i>
              </button>
            </div>
            <div v-else class="pair-placeholder">
              <i class="el-icon-upload2"></i>
              <span>Tải ảnh sau</span>
            </div>
          </el-upload>
        </div>
      </div>

      <div v-if="pair.before && pair.after" class="pair-hint">
        <small>
          Frontend:
          <code>&lt;div class="antra-image-comparison"&gt;</code>
          — ảnh 1: before, ảnh 2: after
        </small>
      </div>
    </div>

    <el-button
      v-if="!single"
      type="primary"
      plain
      size="small"
      icon="el-icon-plus"
      @click="addPair"
    >
      Thêm cặp Before / After
    </el-button>
  </div>
</template>

<script>
import imageCompression from "browser-image-compression";

/**
 * v-model: mảng cặp ảnh (mặc định) hoặc 1 object khi single=true
 *
 * [
 *   { before: "/uploads/a.jpg", after: "/uploads/b.jpg", title: "Phòng khách", status: 1 }
 * ]
 *
 * Blade / frontend (twentytwenty):
 * <div class="antra-image-comparison">
 *   <img src="{{ url($item->before) }}" alt="Before">
 *   <img src="{{ url($item->after) }}" alt="After">
 * </div>
 */
const emptyPair = () => ({
  uid: `pair-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
  before: "",
  after: "",
  title: "",
  status: 1,
});

export default {
  name: "BeforeAfterUpload",
  props: {
    value: {
      default: () => [],
    },
    title: {
      type: String,
      default: "",
    },
    single: {
      type: Boolean,
      default: false,
    },
    showTitle: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      pairs: [emptyPair()],
      loading: false,
    };
  },
  watch: {
    value: {
      handler(val) {
        this.pairs = this.normalizeValue(val);
      },
      deep: true,
      immediate: false,
    },
  },
  mounted() {
    this.pairs = this.normalizeValue(this.value);
  },
  methods: {
    resolveUrl(path) {
      if (!path) return "";
      if (/^https?:\/\//i.test(path)) return path;
      const base = typeof __ENV__ !== "undefined" && __ENV__.link ? __ENV__.link : "";
      return base + path.replace(/^\//, "");
    },
    getslugname(text) {
      let slug = (text || "").toLowerCase();
      slug = slug.replace(/e|é|è|ẽ|ẻ|ẹ|ê|ế|ề|ễ|ể|ệ/gi, "e");
      slug = slug.replace(/a|á|à|ã|ả|ạ|ă|ắ|ằ|ẵ|ẳ|ặ|â|ấ|ầ|ẫ|ẩ|ậ/gi, "a");
      slug = slug.replace(/o|ó|ò|õ|ỏ|ọ|ô|ố|ồ|ỗ|ổ|ộ|ơ|ớ|ờ|ỡ|ở|ợ/gi, "o");
      slug = slug.replace(/u|ú|ù|ũ|ủ|ụ|ư|ứ|ừ|ữ|ử|ự/gi, "u");
      slug = slug.replace(/đ/gi, "d");
      slug = slug.replace(/\s*$/g, "").replace(/\s+/g, "-");
      return slug;
    },
    beforeUpload(file) {
      const ok = file.type === "image/jpeg" || file.type === "image/png" || file.type === "image/webp";
      if (!ok) {
        this.$message.error("Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.");
      }
      return ok;
    },
    normalizeValue(val) {
      if (this.single) {
        const row = val && typeof val === "object" && !Array.isArray(val) ? val : {};
        return [
          {
            ...emptyPair(),
            before: row.before || "",
            after: row.after || "",
            title: row.title || "",
            status: row.status !== undefined ? row.status : 1,
            uid: row.uid || emptyPair().uid,
          },
        ];
      }
      if (!val || !Array.isArray(val) || !val.length) {
        return [emptyPair()];
      }
      return val.map((item, index) => ({
        ...emptyPair(),
        ...item,
        before: item.before || "",
        after: item.after || "",
        title: item.title || "",
        status: item.status !== undefined ? item.status : 1,
        uid: item.uid || `pair-index-${index}`,
      }));
    },
    formatOutput() {
      const rows = this.pairs.map(pair => ({
        before: pair.before || "",
        after: pair.after || "",
        title: pair.title || "",
        status: pair.status !== undefined ? pair.status : 1,
      }));
      if (this.single) {
        return rows[0] || { before: "", after: "", title: "", status: 1 };
      }
      return rows;
    },
    emitInput() {
      this.$emit("input", this.formatOutput());
    },
    addPair() {
      this.pairs.push(emptyPair());
      this.emitInput();
    },
    removePair(index) {
      this.pairs.splice(index, 1);
      if (!this.pairs.length) {
        this.pairs.push(emptyPair());
      }
      this.emitInput();
    },
    clearSide(pair, side) {
      pair[side] = "";
      this.emitInput();
    },
    uploadSide(req, pair, side) {
      const loadingKey = side + "Uploading";
      this.$set(pair, loadingKey, true);
      this.uploadFile(req.file)
        .then(path => {
          pair[side] = path;
          this.emitInput();
          if (req.onSuccess) req.onSuccess();
        })
        .catch(() => {
          if (req.onError) req.onError();
        })
        .finally(() => {
          this.$set(pair, loadingKey, false);
        });
    },
    /** Ảnh < 900KB: upload thẳng, không nén (tránh chờ 10–15s). */
    prepareFileForUpload(file) {
      const skipCompressBelow = 900 * 1024;
      if (file.size <= skipCompressBelow) {
        return Promise.resolve(file);
      }
      return imageCompression(file, {
        maxSizeMB: 1.2,
        maxWidthOrHeight: 2560,
        useWebWorker: file.size > 1.5 * 1024 * 1024,
        maxIteration: 2,
        initialQuality: 0.85,
      });
    },
    normalizeUploadPath(path) {
      if (!path) return "";
      const base = typeof __ENV__ !== "undefined" && __ENV__.link ? __ENV__.link : "";
      if (base && path.indexOf(base) === 0) {
        return path.replace(base, "/");
      }
      return path;
    },
    uploadFile(file) {
      this.loading = true;
      return this.prepareFileForUpload(file)
        .then(blob => this.postImageForm(blob, file.name))
        .finally(() => {
          this.loading = false;
        });
    },
    postImageForm(blob, filename) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open("POST", __ENV__.link + "api/upload-image");
        xhr.onload = () => {
          if (xhr.status !== 200) {
            this.$notify.error({ message: "HTTP Error: " + xhr.status });
            reject(new Error("HTTP Error"));
            return;
          }
          try {
            const json = JSON.parse(xhr.responseText);
            resolve(this.normalizeUploadPath(json.path));
          } catch (e) {
            reject(e);
          }
        };
        xhr.onerror = () => {
          this.$notify.error({ message: "Upload thất bại" });
          reject(new Error("Upload failed"));
        };
        const formData = new FormData();
        formData.append("img", blob, filename);
        formData.append("title_post", this.getslugname(this.title));
        xhr.send(formData);
      });
    },
  },
};
</script>

<style scoped>
.before-after-upload {
  width: 100%;
}

.before-after-pair {
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  background: #fafafa;
}

.pair-toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.pair-toolbar strong {
  flex: 1;
}

.pair-status {
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 12px;
}

.pair-status--ok {
  background: #e8f5e9;
  color: #2e7d32;
}

.pair-status--warn {
  background: #fff3e0;
  color: #ef6c00;
}

.pair-remove {
  border: none;
  background: transparent;
  color: #f56c6c;
  cursor: pointer;
  font-size: 18px;
  padding: 4px;
}

.pair-upload-grid {
  display: flex;
  align-items: stretch;
  gap: 12px;
}

.pair-upload-col {
  flex: 1;
  min-width: 0;
}

.pair-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
}

.pair-label--before {
  color: #5c6bc0;
}

.pair-label--after {
  color: #43a047;
}

.pair-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bbb;
  font-size: 20px;
  padding-top: 28px;
}

.pair-uploader >>> .el-upload {
  width: 100%;
  display: block;
}

.pair-placeholder,
.pair-preview {
  width: 100%;
  height: 180px;
  border: 1px dashed #c0c4cc;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #fff;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.pair-placeholder i {
  font-size: 28px;
  color: #909399;
  margin-bottom: 6px;
}

.pair-placeholder span {
  font-size: 13px;
  color: #909399;
}

.pair-placeholder--loading {
  border-color: #409eff;
  background: #f5f9ff;
  cursor: wait;
}

.pair-placeholder--loading i {
  font-size: 32px;
  color: #409eff;
  margin-bottom: 8px;
}

.pair-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pair-preview-tag {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(0, 0, 0, 0.65);
  color: #fff;
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 4px;
  text-transform: uppercase;
}

.pair-preview-remove {
  position: absolute;
  top: 8px;
  right: 8px;
  border: none;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  cursor: pointer;
}

.pair-hint {
  margin-top: 10px;
  color: #909399;
}

.pair-hint code {
  font-size: 11px;
  background: #f0f0f0;
  padding: 1px 4px;
  border-radius: 3px;
}

@media (max-width: 768px) {
  .pair-upload-grid {
    flex-direction: column;
  }

  .pair-divider {
    padding: 0;
    transform: rotate(90deg);
  }
}
</style>

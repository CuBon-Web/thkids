<template>
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Danh sách tin tức</h4>
              <vs-button
                type="gradient"
                style="float:right;"
                :disabled="!canCreateBlog"
                @click="goToAddBlog"
              >Thêm mới</vs-button>
              <vs-input icon="search" placeholder="Search" v-model="keyword" @keyup="searchBlog" />

              <div class="crm-list-bulk-toolbar" v-if="selectedIds.length > 0">
                <span class="crm-list-bulk-count">Đã chọn {{ selectedIds.length }}</span>
                <div class="crm-list-bulk-actions">
                  <select class="form-control crm-list-bulk-status-select" v-model="bulkStatusValue">
                    <option value="">Đổi trạng thái</option>
                    <option :value="1">Hiển thị</option>
                    <option :value="0">Ẩn</option>
                  </select>
                  <vs-button
                    color="success"
                    type="filled"
                    :disabled="!canApplyBulkStatus || !canUpdateBlog"
                    @click="applyBulkStatus"
                  >
                    Áp dụng
                  </vs-button>
                  <vs-button
                    color="warning"
                    type="filled"
                    :disabled="!canCreateBlog"
                    @click="confirmBulkDuplicate"
                  >
                    Nhân bản ({{ selectedIds.length }})
                  </vs-button>
                  <vs-button
                    color="danger"
                    type="border"
                    :disabled="!canDeleteBlog"
                    @click="confirmBulkDelete"
                  >
                    Xóa đã chọn ({{ selectedIds.length }})
                  </vs-button>
                </div>
              </div>

              <vs-table stripe :data="list" max-items="20" pagination>
                <template slot="thead">
                  <vs-th>
                    <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll">
                  </vs-th>
                  <vs-th>Tiêu đề</vs-th>
                  <vs-th>Danh mục</vs-th>
                  <vs-th>Danh mục con</vs-th>
                  <vs-th>Loại</vs-th>
                  <vs-th>Trạng thái</vs-th>
                  <vs-th>Hành động</vs-th>
                </template>
                <template slot-scope="{data}">
                  <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>
                      <input
                        type="checkbox"
                        :checked="selectedIds.includes(tr.id)"
                        @change="toggleSelection(tr.id)"
                      >
                    </vs-td>
                    <vs-td>{{ blogTitle(tr) }}</vs-td>
                    <vs-td v-if="tr.cate != null">{{ blogCateName(tr.cate) }}</vs-td>
                    <vs-td v-else>--Trống--</vs-td>
                    <vs-td v-if="tr.type_cate != null">{{ blogCateName(tr.type_cate) }}</vs-td>
                    <vs-td v-else>-----</vs-td>
                    <vs-td v-if="tr.type_news == 'tin-hot'">Tin Hot</vs-td>
                    <vs-td v-else-if="tr.type_news == 'tin-khuyen-mai'">Tin Khuyến Mãi</vs-td>
                    <vs-td v-else>-----</vs-td>
                    <vs-td>
                      <span :class="tr.status == 1 ? 'crm-list-status-chip crm-list-status-on' : 'crm-list-status-chip crm-list-status-off'">
                        {{ tr.status == 1 ? 'Hiển thị' : 'Ẩn' }}
                      </span>
                    </vs-td>
                    <vs-td>
                      <router-link :to="{name:'editBlog',params:{id:tr.id}}">
                        <vs-button
                          vs-type="gradient"
                          size="lagre"
                          color="success"
                          icon="edit"
                        ></vs-button>
                      </router-link>
                      <vs-button vs-type="gradient" size="lagre" color="red" icon="delete_forever" @click="confirmDestroy(tr.id)"></vs-button>
                    </vs-td>
                  </vs-tr>
                </template>
              </vs-table>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>


<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      list: [],
      keyword: "",
      id_item: "",
      selectedIds: [],
      bulkStatusValue: "",
      timer: null,
    };
  },
  computed: {
    permissionSlugs() {
      return this.$store.getters.permissionSlugs || [];
    },
    canCreateBlog() {
      return this.hasPermissionWithManageFallback("blog.create");
    },
    canUpdateBlog() {
      return this.hasPermissionWithManageFallback("blog.update");
    },
    canDeleteBlog() {
      return this.hasPermissionWithManageFallback("blog.delete");
    },
    isAllSelected() {
      return this.list.length > 0 && this.selectedIds.length === this.list.length;
    },
    canApplyBulkStatus() {
      return this.selectedIds.length > 0 && this.bulkStatusValue !== "";
    },
  },
  methods: {
    ...mapActions([
      "listBlog",
      "loadings",
      "deleteBlog",
      "bulkDeleteBlogs",
      "bulkUpdateBlogStatus",
      "bulkDuplicateBlogs",
    ]),
    hasPermissionWithManageFallback(requiredPermission) {
      if (!requiredPermission) return true;
      if (this.permissionSlugs.includes(requiredPermission)) return true;
      if (
        requiredPermission.endsWith(".view") ||
        requiredPermission.endsWith(".create") ||
        requiredPermission.endsWith(".update") ||
        requiredPermission.endsWith(".delete")
      ) {
        const prefix = requiredPermission.split(".").slice(0, -1).join(".");
        return this.permissionSlugs.includes(prefix + ".manage");
      }
      return false;
    },
    blogTitle(row) {
      try {
        return JSON.parse(row.title)[0].content || "";
      } catch (e) {
        return row.title || "";
      }
    },
    blogCateName(cate) {
      try {
        return JSON.parse(cate.name)[0].content || "";
      } catch (e) {
        return cate.name || "";
      }
    },
    goToAddBlog() {
      if (!this.canCreateBlog) {
        this.$forbidden("Bạn không có quyền thêm mới bài viết.");
        return;
      }
      this.$router.push({ name: "addBlogs" });
    },
    listBlogs() {
      this.loadings(true);
      this.listBlog({ keyword: this.keyword })
        .then((response) => {
          this.loadings(false);
          this.list = response.data;
          this.selectedIds = [];
        })
        .catch(() => {
          this.loadings(false);
          this.list = [];
          this.selectedIds = [];
        });
    },
    toggleSelection(id) {
      if (this.selectedIds.includes(id)) {
        this.selectedIds = this.selectedIds.filter((item) => item !== id);
      } else {
        this.selectedIds.push(id);
      }
    },
    toggleSelectAll() {
      if (this.isAllSelected) {
        this.selectedIds = [];
        return;
      }
      this.selectedIds = this.list.map((item) => item.id);
    },
    applyBulkStatus() {
      if (!this.canApplyBulkStatus || !this.canUpdateBlog) return;
      this.loadings(true);
      this.bulkUpdateBlogStatus({
        ids: this.selectedIds,
        status: this.bulkStatusValue,
      })
        .then(() => {
          this.loadings(false);
          this.$success("Cập nhật trạng thái thành công");
          this.bulkStatusValue = "";
          this.listBlogs();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Cập nhật trạng thái thất bại");
        });
    },
    confirmBulkDelete() {
      if (this.selectedIds.length === 0 || !this.canDeleteBlog) return;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: "Bạn có chắc chắn",
        text: `Xóa ${this.selectedIds.length} bài viết đã chọn`,
        accept: this.bulkDelete,
      });
    },
    confirmBulkDuplicate() {
      if (this.selectedIds.length === 0 || !this.canCreateBlog) return;
      this.$vs.dialog({
        type: "confirm",
        color: "warning",
        title: "Bạn có chắc chắn",
        text: `Nhân bản ${this.selectedIds.length} bài viết đã chọn (bản sao mặc định ẩn)`,
        accept: this.bulkDuplicate,
      });
    },
    bulkDelete() {
      this.loadings(true);
      this.bulkDeleteBlogs({ ids: this.selectedIds })
        .then(() => {
          this.loadings(false);
          this.$success("Xóa nhiều bài viết thành công");
          this.listBlogs();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Xóa thất bại");
        });
    },
    bulkDuplicate() {
      this.loadings(true);
      this.bulkDuplicateBlogs({ ids: this.selectedIds })
        .then(() => {
          this.loadings(false);
          this.$success("Nhân bản bài viết thành công");
          this.listBlogs();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Nhân bản thất bại");
        });
    },
    confirmDestroy(id) {
      this.id_item = id;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: "Bạn có chắc chắn",
        text: "Xóa bản tin này",
        accept: this.destroy,
      });
    },
    searchBlog() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
        this.listBlog({ keyword: this.keyword })
          .then((response) => {
            this.list = response.data;
            this.selectedIds = [];
          })
          .catch(() => {
            this.list = [];
            this.selectedIds = [];
          });
      }, 800);
    },
    destroy() {
      this.loadings(true);
      this.deleteBlog({ id: this.id_item })
        .then(() => {
          this.loadings(false);
          this.$success("Xóa thành công");
          this.listBlogs();
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Xóa thất bại");
        });
    },
  },
  mounted() {
    this.listBlogs();
  },
};
</script>

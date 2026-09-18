<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Danh sách tác phẩm</h4>
              <vs-button
                type="gradient"
                :disabled="!$hasPermission('project.create')"
                @click="$goIfAllowed('project.create', { name: 'add_project' }, 'Bạn không có quyền thêm mới tác phẩm.')"
              >Thêm mới</vs-button>
            </div>

            <!-- Bộ lọc -->
            <div class="d-flex flex-wrap mb-3" style="gap:12px;align-items:flex-end">
              <div style="flex:1;min-width:180px;max-width:300px">
                <label class="filter-label">Tìm theo tên</label>
                <el-input placeholder="Nhập tên tác phẩm..." v-model="keyword" clearable />
              </div>
              <div style="flex:1;min-width:150px;max-width:240px">
                <label class="filter-label">Danh mục</label>
                <el-select v-model="cateFilter" placeholder="Tất cả" clearable style="width:100%">
                  <el-option value="" label="Tất cả danh mục" />
                  <el-option v-for="(c, i) in cates" :key="i" :value="c.id" :label="c.name" />
                </el-select>
              </div>
              <div style="flex:1;min-width:130px;max-width:200px">
                <label class="filter-label">Trạng thái</label>
                <el-select v-model="statusFilter" placeholder="Tất cả" clearable style="width:100%">
                  <el-option value="" label="Tất cả" />
                  <el-option :value="1" label="Hiện" />
                  <el-option :value="0" label="Ẩn" />
                </el-select>
              </div>
              <div>
                <vs-button color="dark" type="border" @click="resetFilter">Xóa lọc</vs-button>
              </div>
            </div>

            <!-- Bulk action bar -->
            <transition name="fade">
              <div v-if="selected.length > 0" class="bulk-bar mb-2">
                <span class="bulk-count">Đã chọn {{ selected.length }} tác phẩm</span>
                <vs-button
                  size="small"
                  color="primary"
                  :disabled="!$hasPermission('project.create')"
                  @click="confirmBulkDuplicate"
                >
                  <i class="material-icons" style="font-size:15px;vertical-align:middle">content_copy</i>
                  Nhân bản
                </vs-button>
                <vs-button size="small" color="danger" @click="confirmBulkDelete">
                  <i class="material-icons" style="font-size:15px;vertical-align:middle">delete</i>
                  Xóa đã chọn
                </vs-button>
                <el-select
                  v-model="bulkCate"
                  placeholder="Chuyển danh mục..."
                  clearable
                  size="small"
                  style="width:180px"
                  @change="bulkChangeCate"
                >
                  <el-option v-for="c in cates" :key="c.id" :value="c.id" :label="c.name" />
                </el-select>
                <el-select
                  v-model="bulkStatus"
                  placeholder="Đổi trạng thái..."
                  clearable
                  size="small"
                  style="width:150px"
                  @change="bulkChangeStatus"
                >
                  <el-option :value="1" label="Hiện" />
                  <el-option :value="0" label="Ẩn" />
                </el-select>
              </div>
            </transition>

            <!-- Table -->
            <vs-table stripe :data="displayList" max-items="15" pagination>
              <template slot="thead">
                <vs-th style="width:40px">
                  <el-checkbox v-model="selectAll" @change="toggleSelectAll" />
                </vs-th>
                <vs-th>Tên tác phẩm</vs-th>
                <vs-th>Danh mục</vs-th>
                <vs-th>Hiển thị</vs-th>
                <vs-th>Nổi bật</vs-th>
                <vs-th>Hành động</vs-th>
              </template>
              <template slot-scope="{ data }">
                <vs-tr :key="tr.id" v-for="tr in data" :class="{ 'tr-selected': isSelected(tr.id) }">
                  <vs-td style="width:40px">
                    <el-checkbox :value="isSelected(tr.id)" @change="toggleSelect(tr.id)" />
                  </vs-td>
                  <vs-td>{{ tr.name }}</vs-td>
                  <vs-td>{{ cateName(tr.project_cate_id) }}</vs-td>
                  <vs-td>
                    <el-switch
                      :value="tr.status == 1"
                      active-color="#13ce66"
                      inactive-color="#ff4949"
                      @change="val => toggleField(tr, 'status', val ? 1 : 0)"
                    />
                  </vs-td>
                  <vs-td>
                    <el-switch
                      :value="tr.show_home == 1"
                      active-color="#409EFF"
                      inactive-color="#C0CCDA"
                      @change="val => toggleField(tr, 'show_home', val ? 1 : 0)"
                    />
                  </vs-td>
                  <vs-td>
                    <router-link :to="{ name: 'edit_project', params: { id: tr.id } }">
                      <vs-button vs-type="gradient" size="small" color="success" icon="edit"></vs-button>
                    </router-link>
                    <vs-button vs-type="gradient" size="small" color="red" icon="delete_forever" @click="confirmDestroy(tr.id)"></vs-button>
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
import { mapActions } from 'vuex';
export default {
  data() {
    return {
      list: [],
      displayList: [],
      cates: [],
      keyword: '',
      cateFilter: '',
      statusFilter: '',
      selected: [],
      selectAll: false,
      bulkCate: '',
      bulkStatus: '',
      id_item: '',
    };
  },
  watch: {
    keyword()      { this.applyFilter(); },
    cateFilter()   { this.applyFilter(); },
    statusFilter() { this.applyFilter(); },
  },
  methods: {
    ...mapActions(['listProject', 'deleteProject', 'bulkDeleteProject', 'bulkDuplicateProject', 'toggleProjectField', 'listProjectCate', 'loadings']),

    loadList() {
      this.loadings(true);
      this.listProject({}).then(response => {
        this.loadings(false);
        this.list = response.data || [];
        this.selected = [];
        this.applyFilter();
      }).catch(() => { this.loadings(false); });
    },
    loadCates() {
      this.listProjectCate({}).then(response => { this.cates = response.data || []; });
    },
    applyFilter() {
      const kw     = (this.keyword || '').toLowerCase().trim();
      const cate   = this.cateFilter;
      const status = this.statusFilter;
      this.displayList = this.list.filter(item => {
        const matchKw     = !kw     || (item.name || '').toLowerCase().includes(kw);
        const matchCate   = !cate   || item.project_cate_id == cate;
        const matchStatus = status === '' || status == null || item.status == status;
        return matchKw && matchCate && matchStatus;
      });
      this.selectAll = false;
    },
    resetFilter() {
      this.keyword = '';
      this.cateFilter = '';
      this.statusFilter = '';
    },
    cateName(id) {
      if (!id) return '—';
      const found = this.cates.find(c => c.id == id);
      return found ? found.name : '—';
    },

    /* ---- Checkbox ---- */
    isSelected(id) { return this.selected.includes(id); },
    toggleSelect(id) {
      const idx = this.selected.indexOf(id);
      idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
      this.selectAll = this.displayList.length > 0 && this.displayList.every(r => this.selected.includes(r.id));
    },
    toggleSelectAll(val) {
      this.selected = val ? this.displayList.map(r => r.id) : [];
    },

    /* ---- Toggle field inline ---- */
    toggleField(row, field, value) {
      this.toggleProjectField({ id: row.id, field, value }).then(() => {
        row[field] = value;
        // force reactivity on displayList
        const idx = this.list.findIndex(r => r.id === row.id);
        if (idx !== -1) this.list[idx][field] = value;
        this.applyFilter();
      }).catch(() => { this.$error('Cập nhật thất bại'); });
    },

    /* ---- Bulk actions ---- */
    confirmBulkDelete() {
      this.$vs.dialog({
        type: 'confirm', color: 'danger',
        title: 'Xóa ' + this.selected.length + ' tác phẩm?',
        text: 'Hành động này không thể hoàn tác.',
        accept: this.doBulkDelete,
      });
    },
    doBulkDelete() {
      this.loadings(true);
      this.bulkDeleteProject({ ids: this.selected }).then(() => {
        this.loadings(false);
        this.$success('Xóa thành công');
        this.loadList();
      }).catch(() => { this.loadings(false); this.$error('Xóa thất bại'); });
    },
    confirmBulkDuplicate() {
      if (!this.$hasPermission('project.create')) {
        this.$error('Bạn không có quyền nhân bản tác phẩm.');
        return;
      }
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Nhân bản ' + this.selected.length + ' tác phẩm?',
        text: 'Tạo bản sao mới (ẩn, không nổi bật) với cùng nội dung và ảnh.',
        accept: this.doBulkDuplicate,
      });
    },
    doBulkDuplicate() {
      this.loadings(true);
      this.bulkDuplicateProject({ ids: this.selected }).then(res => {
        this.loadings(false);
        const count = res.count != null ? res.count : this.selected.length;
        this.$success('Đã nhân bản ' + count + ' tác phẩm');
        this.loadList();
      }).catch(() => {
        this.loadings(false);
        this.$error('Nhân bản thất bại');
      });
    },
    bulkChangeCate(cateId) {
      if (!cateId) return;
      const calls = this.selected.map(id =>
        this.toggleProjectField({ id, field: 'project_cate_id', value: cateId })
      );
      Promise.all(calls).then(() => {
        this.$success('Đã chuyển danh mục');
        this.bulkCate = '';
        this.loadList();
      });
    },
    bulkChangeStatus(val) {
      if (val === '' || val == null) return;
      const calls = this.selected.map(id =>
        this.toggleProjectField({ id, field: 'status', value: val })
      );
      Promise.all(calls).then(() => {
        this.$success('Đã cập nhật trạng thái');
        this.bulkStatus = '';
        this.loadList();
      });
    },

    /* ---- Single delete ---- */
    confirmDestroy(id) {
      this.id_item = id;
      this.$vs.dialog({
        type: 'confirm', color: 'danger',
        title: 'Bạn có chắc chắn?',
        text: 'Xóa tác phẩm này',
        accept: this.destroy,
      });
    },
    destroy() {
      this.deleteProject({ id: this.id_item }).then(() => {
        this.loadings(false);
        this.$success('Xóa thành công');
        this.loadList();
      });
    },
  },
  mounted() {
    this.loadList();
    this.loadCates();
  },
};
</script>

<style scoped>
.filter-label {
  font-size: 12px;
  margin-bottom: 4px;
  display: block;
  color: #666;
}
.bulk-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f0f7ff;
  border: 1px solid #d0e8ff;
  border-radius: 6px;
  padding: 8px 14px;
  flex-wrap: wrap;
}
.bulk-count {
  font-weight: 600;
  font-size: 13px;
  color: #409EFF;
  margin-right: 4px;
}
.tr-selected td {
  background: #f0f7ff !important;
}
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter, .fade-leave-to { opacity: 0; }
</style>

<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Danh mục dự án</h4>
            <vs-button
              type="gradient"
              style="float:right;"
              @click="$router.push({ name: 'add_project_category' })"
            >Thêm mới</vs-button>
            <vs-table max-items="10" pagination :data="list">
              <template slot="thead">
                <vs-th>ID</vs-th>
                <vs-th>Tên danh mục</vs-th>
                <vs-th>Trạng thái</vs-th>
                <vs-th>Hành động</vs-th>
              </template>
              <template slot-scope="{ data }">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td :data="tr.id">{{ tr.id }}</vs-td>
                  <vs-td :data="tr.name">{{ tr.name }}</vs-td>
                  <vs-td>
                    <vs-chip :color="tr.status == 1 ? 'success' : 'danger'">
                      {{ tr.status == 1 ? 'Hiện' : 'Ẩn' }}
                    </vs-chip>
                  </vs-td>
                  <vs-td>
                    <router-link :to="{ name: 'edit_project_category', params: { id: tr.id } }">
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
  data: () => ({
    list: [],
    id_item: '',
  }),
  methods: {
    ...mapActions(['listProjectCate', 'deleteProjectCate', 'loadings']),
    loadList() {
      this.loadings(true);
      this.listProjectCate({}).then(response => {
        this.loadings(false);
        this.list = response.data;
      }).catch(() => { this.loadings(false); });
    },
    confirmDestroy(id) {
      this.id_item = id;
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Bạn có chắc chắn?',
        text: 'Xóa danh mục dự án này',
        accept: this.destroy,
      });
    },
    destroy() {
      this.loadings(true);
      this.deleteProjectCate({ id: this.id_item }).then(() => {
        this.loadings(false);
        this.$success('Xóa thành công');
        this.loadList();
      });
    },
  },
  mounted() {
    this.loadList();
  },
};
</script>

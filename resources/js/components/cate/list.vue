<template>

  <!-- partial -->

  <div>

      <div class="row">

        <div class="col-md-12 grid-margin stretch-card">

          <div class="card">

            <div class="card-body">

              <h4 class="card-title" @click="listCate">Danh sách danh mục</h4>

              <p class="card-description">Thêm mới hoặc sửa chửa danh mục sản phẩm</p>

              <p v-if="canDrag" class="cate-sort-hint">

                Kéo thả biểu tượng <i class="material-icons">drag_indicator</i> để thay đổi thứ tự hiển thị

              </p>

              <p v-else-if="keyword" class="cate-sort-hint cate-sort-hint--warning">

                Xóa từ khóa tìm kiếm để kéo thả sắp xếp danh mục

              </p>

              <vs-button

                type="gradient"

                style="float:right;"

                :disabled="!$hasPermission('product.create')"

                @click="$goIfAllowed('product.create', { name: 'add_category' }, 'Bạn không có quyền thêm mới danh mục sản phẩm.')"

              >Thêm mới</vs-button>

              <vs-input

                icon="search"

                placeholder="Search"

                v-model="keyword"

                @keyup="searchCategory()"

              />

              <div class="table-responsive cate-sort-table-wrap">

                <table class="table table-striped cate-sort-table">

                  <thead>

                    <tr>

                      <th v-if="canDrag" class="cate-sort-col-handle"></th>

                      <th>ID</th>

                      <th>Tên</th>

                      <th>Avatar</th>

                      <th>Title</th>

                      <th>Hành động</th>

                    </tr>

                  </thead>

                  <tbody ref="categoryTbody">

                    <tr v-for="tr in list" :key="tr.id" :data-id="tr.id">

                      <td v-if="canDrag" class="cate-drag-handle" title="Kéo để sắp xếp">

                        <i class="material-icons">drag_indicator</i>

                      </td>

                      <td>{{ tr.id }}</td>

                      <td>{{ getCategoryName(tr.name) }}</td>

                      <td>

                        <vs-avatar size="70px" :src="tr.avatar" />

                      </td>

                      <td>{{ tr.path }}</td>

                      <td>

                        <router-link :to="{name:'edit_category',params:{id:tr.id}}">

                          <vs-button

                            vs-type="gradient"

                            size="lagre"

                            color="success"

                            icon="edit"

                          ></vs-button>

                        </router-link>

                        <vs-button vs-type="gradient" size="lagre" color="red" icon="delete_forever" @click="confirmDestroy(tr.id)"></vs-button>

                      </td>

                    </tr>

                  </tbody>

                </table>

              </div>

            </div>

          </div>

        </div>

      </div>

      <vs-popup style="width:100%;" title="Thêm mới danh mục" :active.sync="popupActivo">

        <ModalAdd @closePopup="closePop($event)" />

      </vs-popup>

  </div>

</template>





<script>

import ModalAdd from "../../components/layouts/modal/category/add";

import Sortable from "sortablejs";

import { mapActions } from "vuex";



export default {

  data: () => ({

    keyword: null,

    popupActivo: false,

    list: [],

    timer: 0,

    id_item: "",

    sortableInstance: null,

    isSavingSort: false

  }),

  components: {

    ModalAdd

  },

  computed: {

    canDrag() {

      return !this.keyword && this.$hasPermission("product.update");

    }

  },

  watch: {

    list() {

      this.$nextTick(() => {

        this.initSortable();

      });

    },

    canDrag() {

      this.$nextTick(() => {

        this.initSortable();

      });

    }

  },

  methods: {

    ...mapActions(["listCate", "destroyCate", "sortCategory", "loadings"]),

    getCategoryName(name) {

      try {

        return JSON.parse(name)[0].content;

      } catch (e) {

        return name;

      }

    },

    closePop(event) {

      this.listCategory();

      this.popupActivo = event;

    },

    listCategory() {

      this.loadings(true);

      this.listCate({ keyword: this.keyword })

        .then(response => {

          this.loadings(false);

          this.list = response.data;

        });

    },

    searchCategory() {

      if (this.timer) {

        clearTimeout(this.timer);

        this.timer = null;

      }

      this.timer = setTimeout(() => {

        this.listCate({ keyword: this.keyword })

          .then(response => {

            this.list = response.data;

          });

      }, 800);

    },

    destroy() {

      this.loadings(true);

      this.destroyCate({ id: this.id_item })

        .then(() => {

          this.listCategory();

          this.loadings(false);

          this.$success("Xóa danh mục thành công");

        });

    },

    confirmDestroy(id) {

      this.id_item = id;

      this.$vs.dialog({

        type: "confirm",

        color: "danger",

        title: "Bạn có chắc chắn",

        text: "Xóa danh mục này",

        accept: this.destroy

      });

    },

    initSortable() {

      this.destroySortable();

      if (!this.canDrag || !this.$refs.categoryTbody) {

        return;

      }

      this.sortableInstance = Sortable.create(this.$refs.categoryTbody, {

        animation: 150,

        handle: ".cate-drag-handle",

        ghostClass: "cate-sortable-ghost",

        onEnd: this.onSortEnd

      });

    },

    destroySortable() {

      if (this.sortableInstance) {

        this.sortableInstance.destroy();

        this.sortableInstance = null;

      }

    },

    onSortEnd(evt) {

      const { oldIndex, newIndex } = evt;

      if (oldIndex === newIndex || this.isSavingSort) {

        return;

      }

      const nextList = [...this.list];

      const moved = nextList.splice(oldIndex, 1)[0];

      nextList.splice(newIndex, 0, moved);

      this.list = nextList;

      this.saveSortOrder();

    },

    saveSortOrder() {

      if (this.isSavingSort) {

        return;

      }

      this.isSavingSort = true;

      const ids = this.list.map(item => item.id);

      this.sortCategory({ ids })

        .then(() => {

          this.$success("Cập nhật thứ tự hiển thị thành công");

        })

        .catch(() => {

          this.$error("Cập nhật thứ tự thất bại");

          this.listCategory();

        })

        .finally(() => {

          this.isSavingSort = false;

        });

    }

  },

  mounted() {

    this.listCategory();

  },

  beforeDestroy() {

    this.destroySortable();

  }

};

</script>

<style>

.cate-sort-hint {

  margin: 0 0 12px;

  font-size: 13px;

  color: #6c757d;

}



.cate-sort-hint .material-icons {

  font-size: 16px;

  vertical-align: middle;

}



.cate-sort-hint--warning {

  color: #e65100;

}



.cate-sort-table-wrap {

  margin-top: 16px;

}



.cate-sort-table th,

.cate-sort-table td {

  vertical-align: middle;

}



.cate-sort-col-handle {

  width: 48px;

}



.cate-drag-handle {

  width: 48px;

  text-align: center;

  cursor: grab;

  color: #9e9e9e;

  user-select: none;

}



.cate-drag-handle:active {

  cursor: grabbing;

}



.cate-sortable-ghost {

  opacity: 0.45;

  background: #e8f5e9 !important;

}

</style>




<template>
  <div class="container mt-4">
    <h2 class="mb-4">จัดการประเภทสินค้า</h2>

    <!-- ฟอร์มเพิ่มและแก้ไข -->
    <div class="card p-3 mb-4">
      <label class="form-label">ชื่อประเภทสินค้า</label>

      <div class="d-flex gap-2">
        <input
          v-model="categoryName"
          type="text"
          class="form-control"
          placeholder="กรอกชื่อประเภทสินค้า"
          @keyup.enter="saveCategory"
        />

        <button
          class="btn btn-success"
          @click="saveCategory"
          :disabled="!categoryName.trim()"
        >
          {{ editId ? "บันทึกการแก้ไข" : "เพิ่มประเภทสินค้า" }}
        </button>

        <button
          v-if="editId"
          class="btn btn-secondary"
          @click="cancelEdit"
        >
          ยกเลิก
        </button>
      </div>
    </div>

    <!-- ตารางข้อมูล -->
    <div class="card p-3">
      <h5 class="mb-3">รายการประเภทสินค้า</h5>

      <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
          <thead class="table-primary">
            <tr>
              <th>รหัสประเภท</th>
              <th>ชื่อประเภทสินค้า</th>
              <th>จัดการ</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in categories" :key="item.category_id">
              <td>{{ item.category_id }}</td>
              <td>{{ item.category_name }}</td>
              <td>
                <button
                  class="btn btn-warning btn-sm me-2"
                  @click="editCategory(item)"
                >
                  แก้ไข
                </button>

                <button
                  class="btn btn-danger btn-sm"
                  @click="deleteCategory(item.category_id)"
                >
                  ลบ
                </button>
              </td>
            </tr>

            <tr v-if="categories.length === 0">
              <td colspan="3">ยังไม่มีข้อมูลประเภทสินค้า</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Categorys",

  data() {
    return {
      // เปลี่ยน URL ให้ตรงกับตำแหน่ง PHP ของคุณ
      apiUrl: "http://10.254.215.27/MK_Shop/php_api/category.php",

      categories: [],
      categoryName: "",
      editId: null
    };
  },

  mounted() {
    this.loadCategories();
  },

  methods: {
    // โหลดข้อมูล
    async loadCategories() {
      try {
        const response = await fetch(
          this.apiUrl + "?action=list"
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "โหลดข้อมูลไม่สำเร็จ");
        }

        this.categories = result.data;

      } catch (error) {
        alert("โหลดข้อมูลไม่สำเร็จ: " + error.message);
      }
    },

    // เพิ่มหรือแก้ไขข้อมูล
    async saveCategory() {
      const name = this.categoryName.trim();

      if (!name) {
        alert("กรุณากรอกชื่อประเภทสินค้า");
        return;
      }

      const action = this.editId ? "update" : "add";

      const data = {
        category_name: name
      };

      if (this.editId) {
        data.category_id = this.editId;
      }

      try {
        const response = await fetch(
          this.apiUrl + "?action=" + action,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
          }
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "บันทึกข้อมูลไม่สำเร็จ");
        }

        alert(result.message);

        this.categoryName = "";
        this.editId = null;

        await this.loadCategories();

      } catch (error) {
        alert("เกิดข้อผิดพลาด: " + error.message);
      }
    },

    // เลือกข้อมูลเพื่อแก้ไข
    editCategory(item) {
      this.editId = item.category_id;
      this.categoryName = item.category_name;
    },

    // ยกเลิกการแก้ไข
    cancelEdit() {
      this.editId = null;
      this.categoryName = "";
    },

    // ลบข้อมูล
    async deleteCategory(id) {
      if (!confirm("ต้องการลบประเภทสินค้านี้หรือไม่?")) {
        return;
      }

      try {
        const response = await fetch(
          this.apiUrl + "?action=delete",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              category_id: id
            })
          }
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "ลบข้อมูลไม่สำเร็จ");
        }

        alert(result.message);

        await this.loadCategories();

      } catch (error) {
        alert("เกิดข้อผิดพลาด: " + error.message);
      }
    }
  }
};
</script>

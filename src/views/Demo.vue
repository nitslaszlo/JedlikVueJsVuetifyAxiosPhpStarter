<template>
  <v-app id="inspire">
    <v-form>
      <div>
        <v-dialog v-model="dialog" max-width="800px">
          <v-btn slot="activator" color="primary" dark class="mb-2">New Item</v-btn>
          <v-card>
            <v-card-title>
              <span class="headline">{{ editing ? "Edit item":"New item" }}</span>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.name" label="Név"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.calories" label="Kalóra"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.fatPercent" label="Zsír százalék"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.isPaleo" label="Paleo"></v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
              <v-btn v-if="!editing" color="blue darken-1" flat @click.native="addNewItem">Add</v-btn>
              <v-btn v-if="editing" color="blue darken-1" flat @click.native="updateItem">Update</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-data-table :headers="headers" :items="desserts" hide-actions class="elevation-1">
          <template slot="items" slot-scope="props">
            <td>{{ props.item.name }}</td>
            <td class="text-xs-right">{{ props.item.calories }}</td>
            <td class="text-xs-right">{{ props.item.fatPercent }}</td>
            <td class="text-xs-right">{{ props.item.isPaleo }}</td>
            <td class="text-xs-right">{{ props.item.created }}</td>
            <td class="text-xs-right">{{ props.item.edited }}</td>
            <td class="justify-center layout px-0">
              <v-btn icon class="mx-0" @click="editItem(props.item)">
                <v-icon color="teal">edit</v-icon>
              </v-btn>
              <v-btn icon class="mx-0" @click="deleteItem(props.item)">
                <v-icon color="pink">delete</v-icon>
              </v-btn>
            </td>
          </template>
        </v-data-table>
      </div>
    </v-form>
  </v-app>
</template>

<script  lang="ts">
import { Component, Vue, Watch } from "vue-property-decorator";
import axios, { AxiosResponse, AxiosError } from "axios";

interface iHeaders {
  text: string;
  align?: string;
  sortable?: boolean;
  value: string;
}

interface iDessertFull {
  id: string;
  name: string;
  calories: string;
  fatPercent: string;
  isPaleo: string;
  created: string;
  edited: string;
}

interface iDessertShort {
  name: string;
  calories: string;
  fatPercent: string;
  isPaleo: string;
}

@Component
export default class Demo extends Vue {
  private dialog: boolean = false;
  private editing: boolean = false;
  private errorMessage: string = "";
  private successMessage: string = "";
  private desserts: iDessertFull[] = [];
  private editedItem: iDessertShort = {
    name: "",
    calories: "",
    fatPercent: "",
    isPaleo: ""
  };
  private editedIndex: number;
  private defaultItem: iDessertShort = {
    name: "",
    calories: "",
    fatPercent: "",
    isPaleo: ""
  };

  private headers: iHeaders[] = [
    { text: "Név", align: "left", sortable: false, value: "name" },
    { text: "Kalória", value: "calories" },
    { text: "Zsír százalék", value: "fatPercent" },
    { text: "Paleó", value: "isPaleo" },
    { text: "Létrehozva", value: "created" },
    { text: "Szerkesztve", value: "edited" }
  ];

  constructor() {
    super();
  }

  @Watch("dialog")
  private isDialog_changed(): any {
    // console.log(this.dialog);
  }

  private GetAllDessert(): void {
    axios
      .get("http://localhost/api.php?action=read")
      .then((res: AxiosResponse) => {
        this.desserts = res.data.desserts;
      })
      .catch((ex: AxiosError) => {
        console.log(ex.message);
      });
  }

  private editItem(item: iDessertShort): void {
    this.editedItem = item;
    this.editing = true;
    this.dialog = true;
  }

  private deleteItem(item: iDessertFull): void {
    if (confirm("Are you sure you want to delete this item?")) {
      var formData = this.toFormData(item);
      axios
        .post("http://localhost/api.php?action=delete", formData)
        .then(response => {
          console.log(response);
          if (response.data.error) {
            this.errorMessage = response.data.message;
          } else {
            this.successMessage = response.data.message;
            this.GetAllDessert();
          }
        });
    }
  }

  private updateItem(): void {
    var formData = this.toFormData(this.editedItem);
    axios
      .post("http://localhost/api.php?action=update", formData)
      .then(response => {
        console.log(response);
        if (response.data.error) {
          this.errorMessage = response.data.message;
        } else {
          this.successMessage = response.data.message;
          this.GetAllDessert();
        }
      });
    this.close();
  }

  private close(): void {
    this.dialog = false;
    setTimeout(() => {
      this.editing = false;
      this.editedItem = this.defaultItem;
    }, 300);
  }

  private addNewItem(): void {
    var formData = this.toFormData(this.editedItem);
    axios
      .post("http://localhost/api.php?action=create", formData)
      .then(response => {
        console.log(response);
        // app.newUser = { username: "", email: "", mobile: "" };
        if (response.data.error) {
          this.errorMessage = response.data.message;
          alert(this.errorMessage);
        } else {
          // this.successMessage = response.data.message;
          this.GetAllDessert();
        }
      });
    this.close();
  }

  private toFormData(dessert: iDessertShort) {
    var form_data = new FormData();
    for (let [key, value] of Object.entries(dessert)) {
      form_data.append(key, value);
    }
    return form_data;
  }

  mounted() {
    this.GetAllDessert();
  }
}
</script>

<style scoped>
</style>

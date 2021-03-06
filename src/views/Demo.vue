<template>
  <v-app id="inspire">
    <div>
      <v-btn color="error" dark class="mb-2" @click.native="open">New Item</v-btn>
      <v-dialog v-model="dialog" max-width="800px">
        <v-form ref="form" v-model="valid">
          <v-card>
            <v-card-title>
              <span class="headline">{{ editing ? "Edit item":"New item" }}</span>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      v-model="editedItem.name"
                      label="Név"
                      :rules="[x => !!x || 'Required field']"
                      required
                      counter
                      maxlength="25"
                    ></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      v-model="editedItem.calories"
                      label="Kalória"
                      suffix="kcal"
                      :rules="[x => Number.isInteger(parseFloat(x)) || 'Integer number required', x=> parseInt(x) >= 0 || 'Positive number required' ]"
                      type="number"
                    ></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      v-model="editedItem.fatPercent"
                      label="Zsírszázalék"
                      suffix="%"
                      :rules="[x=> parseInt(x) >= 0 || 'Positive number required', x=> parseInt(x) <= 100 || 'Max number is 100' ]"
                      type="number"
                    ></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-checkbox
                      v-model="editedItem.isPaleo"
                      label="Paleo"
                      :true-value="'1'"
                      :false-value="'0'"
                    ></v-checkbox>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
              <v-btn
                v-if="!editing"
                color="blue darken-1"
                :class="{red: !valid, green: valid}"
                :disabled="!valid"
                flat
                @click.native="addNewItem"
              >Add</v-btn>
              <v-btn
                v-if="editing"
                color="blue darken-1"
                :class="{red: !valid || !isChanged(), green: valid && isChanged() }"
                :disabled="!valid || !isChanged()"
                flat
                @click.native="updateItem"
              >Update</v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-dialog>
      <v-data-table
        :headers="headers"
        :items="desserts"
        :rows-per-page-items="[10, 15, 30, 50]"
        class="elevation-1"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.name }}</td>
          <td class="text-xs-right">{{ props.item.calories }}</td>
          <td class="text-xs-right">{{ props.item.fatPercent }}</td>
          <td class="text-xs-right">{{ props.item.isPaleo == 0 ? "false" : "true" }}</td>
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
      <p>Last message: {{ lastMessage }}</p>
    </div>
  </v-app>
</template>

<script  lang="ts">
import { Component, Vue, Watch } from "vue-property-decorator";
import axios, { AxiosResponse, AxiosError } from "axios";

interface IHeaders {
  text: string;
  align?: string;
  sortable?: boolean;
  value: string;
}

interface IDessertFull {
  id: string;
  name: string;
  calories: string;
  fatPercent: string;
  isPaleo: string;
  created: string;
  edited: string;
}

interface IDessertShort {
  name: string;
  calories: string;
  fatPercent: string;
  isPaleo: string;
}

@Component
export default class Demo extends Vue {
  private dialog: boolean = false;
  private valid: boolean = false;
  private editing: boolean = false;
  private lastMessage: string = "";
  private desserts: IDessertFull[] = [];
  private defaultItem: IDessertShort = {
    name: "",
    calories: "0.0",
    fatPercent: "0",
    isPaleo: "0"
  };
  private editedItem: IDessertShort = {
    name: "",
    calories: "0.0",
    fatPercent: "0",
    isPaleo: "0"
  };

  private itemBeforeEdit: IDessertShort = {
    name: "",
    calories: "0.0",
    fatPercent: "0",
    isPaleo: "0"
  };

  private headers: IHeaders[] = [
    { text: "Név", align: "left", sortable: false, value: "name" },
    { text: "Kalória", value: "calories" },
    { text: "Zsírszázalék", value: "fatPercent" },
    { text: "Paleó", value: "isPaleo" },
    { text: "Létrehozva", value: "created" },
    { text: "Szerkesztve", value: "edited" }
  ];

  public mounted() {
    this.getAllDessert();
  }

  @Watch("dialog")
  private isDialogChanged(): any {
    // console.log(this.dialog);
  }

  private isChanged(): boolean {
    const changed: boolean = !(
      this.editedItem.name === this.itemBeforeEdit.name &&
      this.editedItem.calories === this.itemBeforeEdit.calories &&
      this.editedItem.fatPercent === this.itemBeforeEdit.fatPercent &&
      this.editedItem.isPaleo === this.itemBeforeEdit.isPaleo
    );
    return changed;
  }

  private getAllDessert(): void {
    axios
      .get("http://localhost/api.php?action=read")
      .then((res: AxiosResponse) => {
        this.desserts = res.data.desserts;
      })
      .catch((ex: AxiosError) => {
        alert(ex.message);
      });
  }

  private editItem(item: IDessertShort): void {
    this.editedItem = Object.assign({}, item);
    this.itemBeforeEdit = Object.assign({}, item);
    this.editing = true;
    this.dialog = true;
  }

  private deleteItem(item: IDessertFull): void {
    if (confirm("Are you sure you want to delete this item?")) {
      const formData = this.toFormData(item);
      axios
        .post("http://localhost/api.php?action=delete", formData)
        .then(response => {
          this.lastMessage = response.data.message;
          if (response.data.error) {
            console.log(response);
          } else {
            this.getAllDessert();
          }
        });
    }
  }

  private updateItem(): void {
    const formData = this.toFormData(this.editedItem);
    axios
      .post("http://localhost/api.php?action=update", formData)
      .then(response => {
        this.lastMessage = response.data.message;
        if (response.data.error) {
          console.log(response);
        } else {
          this.getAllDessert();
        }
      });
    this.close();
  }

  private open(): void {
    const temp: any = this.$refs.form;
    if (temp) {
      temp.resetValidation();
    }
    this.editedItem = Object.assign({}, this.defaultItem);
    this.dialog = true;
    this.editing = false;
  }

  private close(): void {
    this.dialog = false;
    setTimeout(() => {
      this.editing = false;
    }, 300);
  }

  private addNewItem(): void {
    const formData = this.toFormData(this.editedItem);
    axios
      .post("http://localhost/api.php?action=create", formData)
      .then(response => {
        this.lastMessage = response.data.message;
        if (response.data.error) {
          console.log(response);
        } else {
          this.getAllDessert();
        }
      });
    this.close();
  }

  private toFormData(dessert: IDessertShort) {
    const formData = new FormData();
    for (const [key, value] of Object.entries(dessert)) {
      formData.append(key, value);
    }
    return formData;
  }
}
</script>

<style scoped>
</style>

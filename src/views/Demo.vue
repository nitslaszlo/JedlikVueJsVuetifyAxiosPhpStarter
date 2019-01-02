<template>
  <v-app id="inspire">
    <v-form>
      <div>
        <v-dialog v-model="dialog" max-width="500px">
          <v-btn slot="activator" color="primary" dark class="mb-2">New Item</v-btn>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
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
                </v-layout>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
              <v-btn color="blue darken-1" flat @click.native="save">Save</v-btn>
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

interface iDessert {
  name: string;
  calories: number;
  fatPercent: number;
  isPaleo: boolean;
  created: string;
  edited: string;
}

@Component
export default class Demo extends Vue {
  private dialog: boolean = false;
  private errorMessage: string = "";
  private desserts: iDessert[] = [];
  private editedItem: iDessert;
  private editedIndex: number;
  private defaultItem: iDessert;
  private listPrimitive: any = null;

  // eslint-disable-next-line

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
    this.defaultItem.name = "";
    this.defaultItem.calories = 0;
    this.defaultItem.fatPercent = 0.0;
    this.defaultItem.isPaleo = false;
    this.defaultItem.created = new Date()
      .toISOString()
      .slice(0, 19)
      .replace("T", " ");
  }

  private get formTitle(): string {
    return this.editedIndex === -1 ? "New Item" : "Edit Item";
  }

  // @Watch
  // this.dialog;
  // The watch property watches dialog for when its value changes.
  // If the value changes to false, it calls the function close() which will be defined later.

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

  private editItem(item: iDessert): void {
    this.editedIndex = this.desserts.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  private deleteItem(item: iDessert): void {
    const index = this.desserts.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.listPrimitive.delete(index);
  }

  private close(): void {
    this.dialog = false;
    setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
    }, 300);
  }

  private save(): void {
    if (this.editedIndex > -1) {
      this.listPrimitive.update(this.editedIndex, this.editedItem);
    } else {
      this.listPrimitive.push(this.editedItem);
    }
  }

  private OnClick(művelet: string): void {
    window.alert(művelet);
  }

  mounted() {
    this.GetAllDessert();
  }
}
</script>

<style scoped>
</style>

<template>

    <v-data-table :headers="headers" :items="data" :items-per-page="5" class="elevation-1">
        <template v-slot:item="row">
            <tr>
                <td>{{row.item.name}}</td>
                <td>
                    <v-icon class="mx-1"  color="blue" @click="editRow(row.item.id)">edit</v-icon>
                    <v-icon class="mx-1"  color="red" @click="deleteRow(row.item.id)">delete</v-icon>
                </td>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
  export default {
    data () {
      return {
        headers: [
          { text: 'Name' },
          { text: 'Actions' },
        ],
        data: [
          {
            id: 159,
            name: 'Frozen Yogurt',
          }
        ],
      }
    },
     methods: {
      editRow(id) {
        console.log(id);
      },
      deleteRow(id){
        console.log(id);
      }
    },
  }
</script>
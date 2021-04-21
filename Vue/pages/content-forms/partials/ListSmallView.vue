<script src="./ListSmallViewJs.js"></script>
<template>
    <div>

        <b-table :data="page.list_is_empty ? [] : page.list.data"
                 :hoverable="true"
                 :row-class="setRowClass">

            <template  >

                <b-table-column v-slot="props" field="name" label="Name">
                    <b-tooltip label="Copy Slug" type="is-dark">
                        <vh-copy class="text-copyable"
                                 :data="props.row.slug"
                                 :label="props.row.name"
                                 @copied="copiedData"
                        >
                        </vh-copy>

                    </b-tooltip>
                </b-table-column>

                <b-table-column v-slot="props" field="actions" label=""
                                width="80">

                    <b-tooltip label="Content Structure" type="is-dark">
                        <b-button size="is-small"
                                  @click="toContentStructure(props.row)"
                                  icon-left="align-left">
                        </b-button>
                    </b-tooltip>

                    <b-tooltip label="View" type="is-dark">
                        <b-button size="is-small"
                                  @click="setActiveItem(props.row)"
                                  icon-left="chevron-right">
                        </b-button>
                    </b-tooltip>


                </b-table-column>

            </template>

            <template slot="empty">
                <section class="section">
                    <div class="content has-text-grey has-text-centered">
                        <p>Nothing here.</p>
                    </div>
                </section>
            </template>

        </b-table>
    </div>
</template>


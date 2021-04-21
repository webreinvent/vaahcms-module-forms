<script src="./ListLargeViewJs.js"></script>
<template>
    <div>
        <b-table :data="page.list_is_empty ? [] : page.list.data"
                 :checkable="true"
                 :checked-rows.sync="page.bulk_action.selected_items"
                 checkbox-position="left"
                 :hoverable="true"
                 :row-class="setRowClass">

            <template  >
                <b-table-column v-slot="props" field="id" label="ID" width="85" >
                    {{ props.row.id }}
                </b-table-column>

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

                <b-table-column v-slot="props" field="plural" label="Plural">

                    <b-tooltip label="Copy Plural Slug" type="is-dark">
                        <vh-copy class="text-copyable"
                                 :data="props.row.plural_slug"
                                 :label="props.row.plural"
                                 @copied="copiedData"
                        >
                        </vh-copy>

                    </b-tooltip>
                </b-table-column>

                <b-table-column v-slot="props" field="singular" label="Singular">
                    <b-tooltip label="Copy Singular Slug" type="is-dark">
                        <vh-copy class="text-copyable"
                                 :data="props.row.singular_slug"
                                 :label="props.row.singular"
                                 @copied="copiedData"
                        >
                        </vh-copy>

                    </b-tooltip>
                </b-table-column>

                <b-table-column v-slot="props" width="100"
                                field="is_published" label="Is Published">

                    <div v-if="props.row.deleted_at">

                        <b-button v-if="props.row.is_published === 1"
                                  disabled rounded size="is-small"
                                  type="is-success">
                            Yes
                        </b-button>
                        <b-button v-else rounded size="is-small" disabled type="is-danger">
                            No
                        </b-button>

                    </div>

                    <div v-else>
                        <b-tooltip label="Change Status" type="is-dark">
                        <b-button v-if="props.row.is_published === 1" rounded size="is-small"
                                  type="is-success" @click="changeStatus(props.row.id)">
                            Yes
                        </b-button>
                        <b-button v-else rounded size="is-small" type="is-danger"
                                  @click="changeStatus(props.row.id)">
                            No
                        </b-button>
                    </b-tooltip>
                    </div>


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


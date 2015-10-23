<?php

/**
 * db_lang.php
 *
 * @package     ark-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

$lang['db_invalid_connection_str'] = 'Tidak dapat menentukan pengaturan database berdasarkan string koneksi yang anda tetapkan.';
$lang['db_unable_to_connect'] = 'Tidak dapat terhubung ke server menggunakan pengaturan yang disediakan.';
$lang['db_unable_to_select'] = 'Tidak dapat memilih database yang ditentukan: %s';
$lang['db_unable_to_create'] = 'Tidak dapat membuat database yang ditentukan: %s';
$lang['db_invalid_query'] = 'Kode query yang anda sampaikan tidak sah.';
$lang['db_must_set_table'] = 'Anda harus menetapkan tabel database yang akan digunakan query anda.';
$lang['db_must_use_set'] = 'Anda harus menggunakan metode "set" untuk memperbaharui entri.';
$lang['db_must_use_index'] = 'Anda harus menetapkan index untuk dicocokkan pada perubahan-perubahan secara batch.';
$lang['db_batch_missing_index'] = 'Satu atau beberapa baris yang dikirim untuk pengubahan secara batch tidak dapat menemukan index yang ditentukan.';
$lang['db_must_use_where'] = 'Tidak diperbolehkan memperbaharui kecuali terdapat klausa "where".';
$lang['db_del_must_use_where'] = 'Tidak diperbolehkan menghapus kecuali terdapat klausa "where" atau "like".';
$lang['db_field_param_missing'] = 'Untuk fetch field diperlukan nama tabel sebagai parameter.';
$lang['db_unsupported_function'] = 'Fitur ini tidak tersedia untuk database yang anda gunakan.';
$lang['db_transaction_failure'] = 'Transaksi gagal: Rollback telah dilakukan.';
$lang['db_unable_to_drop'] = 'Tidak dapat mendrop database yang ditetapkan.';
$lang['db_unsuported_feature'] = 'Fitur tidak didukung oleh platform database yang digunakan.';
$lang['db_unsuported_compression'] = 'Format kompresi berkas yang dipilih tidak didukung oleh server.';
$lang['db_filepath_error'] = 'Tidak dapat menulis data ke path berkas yang telah ditentukan.';
$lang['db_invalid_cache_path'] = 'Path cache yang anda tetapkan tidak sah atau tidak bisa ditulis.';
$lang['db_table_name_required'] = 'Nama tabel diperlukan untuk operasi tersebut.';
$lang['db_column_name_required'] = 'Nama kolom diperlukan untuk operasi tersebut.';
$lang['db_column_definition_required'] = 'Definisi kolom diperlukan untuk operasi tersebut.';
$lang['db_unable_to_set_charset'] = 'Tidak dapat menetapkan koneksi klien dengan set karakter: %s';
$lang['db_error_heading'] = 'Terjadi galat database';

/* End of file db_lang.php */
/* Location: ./system/language/english/db_lang.php */
<?php
namespace App\Http\Controllers;
use App\Models\Verification; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage;
class KycDocumentController extends Controller {public function show(Request $r,Verification $verification,string $type){abort_unless($r->user()->isAdmin()||$r->user()->id===$verification->user_id,403);abort_unless(in_array($type,['id-card','fee-receipt'],true),404);$path=$type==='id-card'?$verification->id_card_image:$verification->fee_receipt_image;abort_unless(Storage::disk('local')->exists($path),404);return Storage::disk('local')->response($path);}}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('fichier');
        $extension = $file->getClientOriginalExtension();

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur de lecture du fichier: ' . $e->getMessage());
        }

        if (count($rows) < 2) {
            return back()->with('error', 'Le fichier est vide ou ne contient que l\'en-tête.');
        }

        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $requiredCols = ['nom', 'prenom', 'email'];
        foreach ($requiredCols as $col) {
            if (!in_array($col, $header)) {
                return back()->with('error', "Colonne requise manquante: $col");
            }
        }

        $colMap = array_flip($header);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $nom = trim($row[$colMap['nom']] ?? '');
            $prenom = trim($row[$colMap['prenom']] ?? '');
            $email = trim($row[$colMap['email']] ?? '');
            $telephone = trim($row[$colMap['telephone'] ?? -1] ?? '');
            $numeroEtudiant = trim($row[$colMap['numero_etudiant'] ?? -1] ?? '');

            if (!$nom || !$prenom || !$email) {
                $skipped++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            User::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone ?: null,
                'numero_etudiant' => $numeroEtudiant ?: null,
                'role' => 'etudiant',
                'password' => Hash::make(Str::random(10)),
                'actif' => true,
            ]);

            $imported++;
        }

        return back()->with('success', "$imported étudiant(s) importé(s) avec succès. $skipped ligne(s) ignorée(s).");
    }

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'nom');
        $sheet->setCellValue('B1', 'prenom');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'telephone');
        $sheet->setCellValue('E1', 'numero_etudiant');

        $sheet->setCellValue('A2', 'Dupont');
        $sheet->setCellValue('B2', 'Jean');
        $sheet->setCellValue('C2', 'jean.dupont@email.com');
        $sheet->setCellValue('D2', '0612345678');
        $sheet->setCellValue('E2', 'ETU-2025-001');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'tpl');
        $writer->save($tempFile);

        return response()->download($tempFile, 'template_import_etudiants.xlsx')->deleteFileAfterSend(true);
    }
}

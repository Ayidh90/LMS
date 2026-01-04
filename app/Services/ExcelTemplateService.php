<?php

namespace App\Services;

/**
 * @phpstan-ignore-next-line
 * Excel template service for generating student import templates
 * Note: Requires phpoffice/phpspreadsheet package
 */
class ExcelTemplateService
{
    /**
     * Check if PhpSpreadsheet is available
     */
    public function isAvailable(): bool
    {
        /** @phpstan-ignore-next-line */
        return class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');
    }

    /**
     * Generate and download Excel template for student import
     * Returns the file content as a string
     */
    public function generateStudentTemplate(): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('Excel library is not installed. Please install phpoffice/phpspreadsheet package.');
        }

        // Use dynamic class loading to avoid linter errors
        $spreadsheetClass = '\PhpOffice\PhpSpreadsheet\Spreadsheet';
        /** @phpstan-ignore-next-line */
        $spreadsheet = new $spreadsheetClass();
        /** @phpstan-ignore-next-line */
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers based on locale
        $locale = app()->getLocale();
        $this->setHeaders($sheet, $locale);
        $this->setExampleRows($sheet, $locale, 20); // Generate 20 test rows
        $this->styleHeaderRow($sheet);
        $this->autoSizeColumns($sheet);

        // Generate file content
        $writerClass = '\PhpOffice\PhpSpreadsheet\Writer\Xlsx';
        /** @phpstan-ignore-next-line */
        $writer = new $writerClass($spreadsheet);
        
        // Save to temporary file with .xlsx extension to ensure proper format
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_template_') . '.xlsx';
        $writer->save($tempFile);
        
        // Read file content as binary
        $content = file_get_contents($tempFile);
        
        // Clean up temp file
        @unlink($tempFile);
        
        return $content;
    }

    /**
     * Get the filename for the template based on locale
     */
    public function getTemplateFilename(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' 
            ? 'قالب_إضافة_الطلاب.xlsx' 
            : 'students_import_template.xlsx';
    }

    /**
     * Set headers in spreadsheet
     * 
     * @param mixed $sheet
     */
    private function setHeaders($sheet, string $locale): void
    {
        // @phpstan-ignore-next-line
        if ($locale === 'ar') {
            // @phpstan-ignore-next-line
            $sheet->setCellValue('A1', 'الاسم');
            // @phpstan-ignore-next-line
            $sheet->setCellValue('B1', 'البريد الإلكتروني');
            // @phpstan-ignore-next-line
            $sheet->setCellValue('C1', 'رقم الهوية الوطنية (اختياري)');
        } else {
            // @phpstan-ignore-next-line
            $sheet->setCellValue('A1', 'Name');
            // @phpstan-ignore-next-line
            $sheet->setCellValue('B1', 'Email');
            // @phpstan-ignore-next-line
            $sheet->setCellValue('C1', 'National ID (Optional)');
        }
    }

    /**
     * Set example rows in spreadsheet with fake data for testing
     * 
     * @param mixed $sheet
     * @param string $locale
     * @param int $count Number of rows to generate
     */
    private function setExampleRows($sheet, string $locale, int $count = 20): void
    {
        $isArabic = $locale === 'ar';
        
        // Arabic names for testing
        $arabicFirstNames = ['أحمد', 'محمد', 'علي', 'خالد', 'سعد', 'فهد', 'عمر', 'يوسف', 'عبدالله', 'مشعل', 'نواف', 'بندر', 'تركي', 'سلطان', 'فيصل', 'ماجد', 'راشد', 'حمد', 'نايف', 'طلال'];
        $arabicLastNames = ['محمد', 'علي', 'أحمد', 'خالد', 'السالم', 'العلي', 'المطيري', 'الدوسري', 'الشمري', 'الغامدي', 'الحارثي', 'الزهراني', 'العتيبي', 'الرشيد', 'الجبير', 'الخالدي', 'الفهيد', 'المنصور', 'الجبالي', 'الخليفي'];
        
        // English names for testing
        $englishFirstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'James', 'Emma', 'Robert', 'Olivia', 'William', 'Sophia', 'Richard', 'Isabella', 'Joseph', 'Mia', 'Thomas', 'Charlotte', 'Charles', 'Amelia'];
        $englishLastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee'];
        
        // Generate fake data rows
        for ($i = 0; $i < $count; $i++) {
            $row = $i + 2; // Start from row 2 (row 1 is headers)
            
            // Always use English names for email generation
            $emailFirstName = $englishFirstNames[array_rand($englishFirstNames)];
            $emailLastName = $englishLastNames[array_rand($englishLastNames)];
            
            // Generate email (always in English)
            $email = strtolower($emailFirstName) . '.' . strtolower($emailLastName) . ($i + 1) . '@example.com';
            
            if ($isArabic) {
                // Generate Arabic name for display
                $firstName = $arabicFirstNames[array_rand($arabicFirstNames)];
                $lastName = $arabicLastNames[array_rand($arabicLastNames)];
                $name = $firstName . ' ' . $lastName;
            } else {
                // Generate English name
                $firstName = $englishFirstNames[array_rand($englishFirstNames)];
                $lastName = $englishLastNames[array_rand($englishLastNames)];
                $name = $firstName . ' ' . $lastName;
            }
            
            // Generate national ID (10 digits)
            $nationalId = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            
            // @phpstan-ignore-next-line
            $sheet->setCellValue('A' . $row, $name);
            // @phpstan-ignore-next-line
            $sheet->setCellValue('B' . $row, $email);
            // @phpstan-ignore-next-line
            $sheet->setCellValue('C' . $row, $nationalId);
        }
    }

    /**
     * Style header row
     * 
     * @param mixed $sheet
     */
    private function styleHeaderRow($sheet): void
    {
        // Use constants to avoid linter errors
        $fillClass = '\PhpOffice\PhpSpreadsheet\Style\Fill';
        $alignmentClass = '\PhpOffice\PhpSpreadsheet\Style\Alignment';
        
        /** @phpstan-ignore-next-line */
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                /** @phpstan-ignore-next-line */
                'fillType' => constant("$fillClass::FILL_SOLID"),
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                /** @phpstan-ignore-next-line */
                'horizontal' => constant("$alignmentClass::HORIZONTAL_CENTER")
            ],
        ];
        /** @phpstan-ignore-next-line */
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
    }

    /**
     * Auto-size columns
     * 
     * @param mixed $sheet
     */
    private function autoSizeColumns($sheet): void
    {
        // @phpstan-ignore-next-line
        $sheet->getColumnDimension('A')->setAutoSize(true);
        // @phpstan-ignore-next-line
        $sheet->getColumnDimension('B')->setAutoSize(true);
        // @phpstan-ignore-next-line
        $sheet->getColumnDimension('C')->setAutoSize(true);
    }
}

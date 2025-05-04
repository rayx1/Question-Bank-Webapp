<?php
/**
 * Extract meaningful keywords from a string.
 */
function extractKeywords($text) {
    // Convert to lowercase and remove punctuation.
    $text = strtolower($text);
    $text = preg_replace("/[^\w\s]/", "", $text);
    
    // Split into words.
    $words = explode(" ", $text);
    
    // Remove common stop words. Expand this list as needed.
    $stopWords = ['the', 'and', 'is', 'in', 'at', 'of', 'a', 'to'];
    $keywords = array_diff($words, $stopWords);
    
    // Remove empty values and duplicate keywords.
    $keywords = array_unique(array_filter($keywords));
    return $keywords;
}

/**
 * Calculate probability using full-text search and recency weighting.
 * The function queries exam_history with questions matching keywords of the new question,
 * for the given subject and year.
 *
 * @param string $questionText The new question text.
 * @param string $subject      Subject of the question.
 * @param string $year         Year of the question.
 * @param mysqli $conn         MySQLi connection.
 * @return float Probability as a percentage.
 */
function calculateAutoProbabilityRecency($questionText, $subject, $year, $conn) {
    // Extract keywords from the question
    $keywords = extractKeywords($questionText);
    if (empty($keywords)) {
        return 0;
    }
    
    // Rebuild keyword string for full-text search.
    $keywordStr = implode(" ", $keywords);
    $subject_safe = $conn->real_escape_string($subject);
    $year_safe    = $conn->real_escape_string($year);
    $keywordStr_safe = $conn->real_escape_string($keywordStr);
    
    // Query similar exam records using full-text search against the question column.
    $querySimilar = "
        SELECT eh.exam_date
        FROM exam_history eh
        JOIN questions q ON eh.question_id = q.id
        WHERE q.subject = '$subject_safe'
          AND q.year = '$year_safe'
          AND MATCH(q.question) AGAINST ('$keywordStr_safe' IN NATURAL LANGUAGE MODE)
    ";
    $resultSimilar = $conn->query($querySimilar);
    
    // Set decay factor for recency weighting. Adjust as needed.
    $decayFactor = 0.01;
    $weightedSum = 0;
    $currentDate = new DateTime();
    
    if($resultSimilar) {
        while ($row = $resultSimilar->fetch_assoc()) {
            $examDate = new DateTime($row['exam_date']);
            $interval = $currentDate->diff($examDate);
            $daysAgo = $interval->days;
            // More recent records carry more weight.
            $weight = exp(-$decayFactor * $daysAgo);
            $weightedSum += $weight;
        }
    }
    
    // Get the baseline: total weighted sum of all exam records (for normalization)
    $queryTotal = "
        SELECT eh.exam_date
        FROM exam_history eh
        JOIN questions q ON eh.question_id = q.id
        WHERE q.subject = '$subject_safe'
          AND q.year = '$year_safe'
    ";
    $resultTotal = $conn->query($queryTotal);
    $totalWeight = 0;
    
    if($resultTotal) {
        while ($row = $resultTotal->fetch_assoc()) {
            $examDate = new DateTime($row['exam_date']);
            $interval = $currentDate->diff($examDate);
            $daysAgo = $interval->days;
            $totalWeight += exp(-$decayFactor * $daysAgo);
        }
    }
    
    if ($totalWeight == 0)
        return 0;
    
    // Calculate probability as weighted ratio (expressed as a percentage)
    $probability = ($weightedSum / $totalWeight) * 100;
    return round($probability, 2);
}
?>

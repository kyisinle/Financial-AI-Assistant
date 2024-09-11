<?php

// Function to extract financial data using regex
function extractFinancialData($text) {
    // Define regex patterns
    $patterns = [
        'income' => '/(?:income|earning|salary|revenue|wage|amount|funds|money|have|has|earn|earns)[^\d]*([\d,]+)/i',
        'expenses' => '/(?:expenses|expense|spending|cost|outlay|expenditure|amount spent|spend|spends|am spending)[^\d]*([\d,]+)/i',
        'principal' => '/(?:principal|amount|sum|capital|loan|on|of|)[^\d]*([\d,]+)/i',
        'rate' => '/(?:rate|interest rate|%|percent|at)[^\d]*([\d,\.]+)/i',
        'time' => '/(?:time|years|after|for|over|duration|period)[^\d]*([\d,]+)/i',
        'monthly_deposit' => '/(?:monthly deposit|saving per month|monthly saving)[^\d]*([\d,]+)/i',
        'debt' => '/(?:debt|loan amount)[^\d]*([\d,]+)/i',
        'current_savings' => '/(?:current savings|savings balance|existing savings)[^\d]*([\d,]+)/i',
        'stock_symbol' => '/(?:stock|share|symbol|stock price)[^\w]*([A-Za-z0-9]+)/i',
        'amount' => '/(?:convert|exchange)[^\d]*([\d,\.]+)/i',
        'from_currency' => '/(?:convert|exchange)[^\d]*([\d,\.]+)\s*([A-Z]{3})[^\w]*to/i',
        'to_currency' => '/(?:to|into)\s*([A-Z]{3})/i',
    ];

    // Extract data using regex patterns
    $results = [];
    foreach ($patterns as $key => $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            // Capture the groups, ensure amount is properly formatted
            if ($key == 'from_currency') {
                // Ensure we capture the currency code correctly and trim spaces
                $results[$key] = isset($matches[2]) ? trim($matches[2]) : null;
            } elseif ($key == 'to_currency') {
                $results[$key] = isset($matches[1]) ? trim($matches[1]) : null;
            } else {
                $results[$key] = isset($matches[1]) ? str_replace(',', '', $matches[1]) : null;
            }
        } else {
            $results[$key] = null;
        }
    }
    return $results;
}

function fetchStockPrice($symbol) {
    $apiKey = 'J70CYAL340O0A2UR'; // Replace with your API key
    $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=1min&apikey=$apiKey";
    
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return "Unable to fetch stock price.";
    }

    $data = json_decode($response, true);
    if (isset($data['Error Message'])) {
        return "Error fetching stock data: " . $data['Error Message'];
    }
    if (isset($data['Time Series (1min)']) && !empty($data['Time Series (1min)'])) {
        $latestTime = array_key_first($data['Time Series (1min)']);
        $latestData = $data['Time Series (1min)'][$latestTime];
        return $latestData['1. open']; // Open price at the latest timestamp
    } else {
        return "Stock symbol not found or no data available.";
    }
}

function convertCurrency($amount, $fromCurrency, $toCurrency) {
    $apiKey = 'J70CYAL340O0A2UR'; // Replace with your API key
    $url = "https://api.fastforex.io/convert?from=$fromCurrency&to=$toCurrency&amount=$amount&api_key=$apiKey";
    
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return "Unable to fetch currency conversion rate.";
    }

    $data = json_decode($response, true);
    if (isset($data['result'])) {
        return $data['result'][$toCurrency] ?? "Currency not supported.";
    } else {
        return "Invalid API response or parameters.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['input'])) {
    $userInput = $_POST['input'];

    // Extract data
    $data = extractFinancialData($userInput);

    $income = isset($data['income']) ? floatval($data['income']) : null;
    $expenses = isset($data['expenses']) ? floatval($data['expenses']) : null;
    $principal = isset($data['principal']) ? floatval($data['principal']) : null;
    $rate = isset($data['rate']) ? floatval($data['rate']) : null;
    $time = isset($data['time']) ? floatval($data['time']) : null;
    $monthly_deposit = isset($data['monthly_deposit']) ? floatval($data['monthly_deposit']) : null;
    $debt = isset($data['debt']) ? floatval($data['debt']) : null;
    $current_savings = isset($data['current_savings']) ? floatval($data['current_savings']) : 0;
    $stock_symbol = isset($data['stock_symbol']) ? $data['stock_symbol'] : null;
    $currency_amount = isset($data['amount']) ? floatval($data['amount']) : null;
    $from_currency = isset($data['from_currency']) ? $data['from_currency'] : null;
    $to_currency = isset($data['to_currency']) ? $data['to_currency'] : null;

    $prologFile = 'financial_assistant.pl';

    // Determine which Prolog rule to run based on keywords in the user input
    $command = null;

    if (stripos($userInput, 'simple interest') !== false) {
        if ($principal !== null && $rate !== null && $time !== null) {
            $command = "swipl -q -s $prologFile -g \"run_simple_interest($principal, $rate, $time), halt.\"";
        }
    }  elseif (stripos($userInput, 'compound interest') !== false) {
        if ($principal !== null && $rate !== null && $time !== null) {
            $command = "swipl -q -s $prologFile -g \"run_compound_interest($principal, $rate, $time), halt.\"";
        }
    } elseif (stripos($userInput, 'emi') !== false || stripos($userInput, 'monthly installment') !== false) {
        if ($principal !== null && $rate !== null && $time !== null) {
            $command = "swipl -q -s $prologFile -g \"run_emi($principal, $rate, $time), halt.\"";
        }
    } elseif (stripos($userInput, 'future saving') !== false || stripos($userInput, 'saving future') !== false) {
        if ($monthly_deposit !== null && $rate !== null && $time !== null) {
            $command = "swipl -q -s $prologFile -g \"run_future_savings($monthly_deposit, $rate, $time), halt.\"";
        }
    } elseif (stripos($userInput, 'retirement fund') !== false) {
        if ($monthly_deposit !== null && $rate !== null && $time !== null) {
            $command = "swipl -q -s $prologFile -g \"run_retirement_fund($current_savings, $monthly_deposit, $rate, $time), halt.\"";
        }
    } elseif (stripos($userInput, 'dti') !== false || stripos($userInput, 'debt-to-income') !== false) {
        if ($debt !== null && $income !== null) {
            $command = "swipl -q -s $prologFile -g \"run_dti($debt, $income), halt.\"";
        }
    } elseif ($income && $expenses) {
        $command = "swipl -q -s $prologFile -g \"run_financial_advice($income, $expenses), halt.\"";
    } elseif ($stock_symbol) {
        $stockPrice = fetchStockPrice($stock_symbol);
        echo "The latest stock price for $stock_symbol is $stockPrice.";
        exit;
    } elseif ($currency_amount !== null && $from_currency !== null && $to_currency !== null) {
        $convertedAmount = convertCurrency($currency_amount, $from_currency, $to_currency);
        echo "$currency_amount $from_currency is equal to $convertedAmount $to_currency.";
        exit;
    }

    if ($command) {
        echo "Running command: $command<br>";
        $output = shell_exec($command);
        if ($output === false) {
            echo "Command failed to execute.";
        } else {
            echo "<pre>$output</pre>";
        }
    } else {
        echo "No command executed.";
    }
} else {
    echo "No input received.";
}
?>

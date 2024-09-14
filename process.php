<?php
session_start();

// Initialize or reset session variables
function initializeSession() {
    $_SESSION['state'] = null;
    $_SESSION['current_question'] = null;
    $_SESSION['data'] = [];
}

function fetchStockPrice($symbol) {
    $apiKey = 'X8VFD9QAQH1AAIU6'; // Replace with your API key
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
    $apiKey = '7fac6527d6-8a4e6e6615-sjm8op'; // Replace with your API key
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

function handleFinancialAdvice() {
    if (empty($_SESSION['data']['income'])) {
        echo "What is your income?";
        $_SESSION['state'] = 'financial_advice';
        $_SESSION['current_question'] = 'income';
    } elseif (empty($_SESSION['data']['expense'])) {
        echo "What is your expense?";
        $_SESSION['current_question'] = 'expense';
    } else {
        $income = $_SESSION['data']['income'];
        $expense = $_SESSION['data']['expense'];       
        $command = "swipl -q -s financial_assistant.pl -g \"run_financial_advice($income, $expense), halt.\"";
        $financialAdvice = shell_exec($command);
        echo $financialAdvice;        
        initializeSession();
    }
}

function handleSimpleInterest() {
    if (empty($_SESSION['data']['principal'])) {
        echo "What is the principal amount?";
        $_SESSION['state'] = 'simple_interest';
        $_SESSION['current_question'] = 'principal';
    } elseif (empty($_SESSION['data']['rate'])) {
        echo "What is the interest rate?";
        $_SESSION['current_question'] = 'rate';
    } elseif (empty($_SESSION['data']['time'])) {
        echo "What is the time period?";
        $_SESSION['current_question'] = 'time';
    } else {
        $principal = $_SESSION['data']['principal'];
        $rate = $_SESSION['data']['rate'];
        $time = $_SESSION['data']['time'];
        $command = "swipl -q -s financial_assistant.pl -g \"run_simple_interest($principal, $rate, $time), halt.\"";
        $simpleInterest = shell_exec($command);
        echo $simpleInterest;
        initializeSession(); // Reset session after processing
    }
}

function handleCompoundInterest() {
    if (empty($_SESSION['data']['principal'])) {
        echo "What is the principal amount?";
        $_SESSION['state'] = 'compound_interest';
        $_SESSION['current_question'] = 'principal';
    } elseif (empty($_SESSION['data']['rate'])) {
        echo "What is the interest rate?";
        $_SESSION['current_question'] = 'rate';
    } elseif (empty($_SESSION['data']['time'])) {
        echo "What is the time period?";
        $_SESSION['current_question'] = 'time';
    } else {
        $principal = $_SESSION['data']['principal'];
        $rate = $_SESSION['data']['rate'];
        $time = $_SESSION['data']['time'];
        $command = "swipl -q -s financial_assistant.pl -g \"run_compound_interest($principal, $rate, $time), halt.\"";
        $compoundInterest = shell_exec($command);
        echo $compoundInterest;
        initializeSession(); // Reset session after processing
    }
}

function handleEMI() {
    if (empty($_SESSION['data']['principal'])) {
        echo "What is the loan amount (principal)?";
        $_SESSION['state'] = 'emi';
        $_SESSION['current_question'] = 'principal';
    } elseif (empty($_SESSION['data']['rate'])) {
        echo "What is the interest rate?";
        $_SESSION['current_question'] = 'rate';
    } elseif (empty($_SESSION['data']['time'])) {
        echo "What is the time period in years?";
        $_SESSION['current_question'] = 'time';
    } else {
        $principal = $_SESSION['data']['principal'];
        $rate = $_SESSION['data']['rate'];
        $time = $_SESSION['data']['time']; 
        $command = "swipl -q -s financial_assistant.pl -g \"run_emi($principal, $rate, $time), halt.\"";
        $emi = shell_exec($command);
        echo $emi;
        initializeSession(); // Reset session after processing
    }
}

function handleDTI() {
    if (empty($_SESSION['data']['debt'])) {
        echo "What is your total debt amount?";
        $_SESSION['state'] = 'dti';
        $_SESSION['current_question'] = 'debt';
    } elseif (empty($_SESSION['data']['income'])) {
        echo "What is your monthly income?";
        $_SESSION['current_question'] = 'income';
    } else {
        $debt = $_SESSION['data']['debt'];
        $income = $_SESSION['data']['income'];
        $command = "swipl -q -s financial_assistant.pl -g \"run_dti($debt, $income), halt.\"";
        $dti = shell_exec($command);
        echo $dti;
        initializeSession(); // Reset session after processing
    }
}

function handleFutureSavings() {
    if (empty($_SESSION['data']['monthly_deposit'])) {
        echo "What is the monthly deposit amount?";
        $_SESSION['state'] = 'future_savings';
        $_SESSION['current_question'] = 'monthly_deposit';
    } elseif (empty($_SESSION['data']['rate'])) {
        echo "What is the interest rate?";
        $_SESSION['current_question'] = 'rate';
    } elseif (empty($_SESSION['data']['time'])) {
        echo "What is the time period in years?";
        $_SESSION['current_question'] = 'time';
    } else {
        $monthlyDeposit = $_SESSION['data']['monthly_deposit'];
        $rate = $_SESSION['data']['rate']; // Monthly interest rate
        $time = $_SESSION['data']['time']; // Number of months
        $command = "swipl -q -s financial_assistant.pl -g \"run_future_savings($monthlyDeposit, $rate, $time), halt.\"";
        $futureSavings = shell_exec($command);
        echo $futureSavings;
        initializeSession(); // Reset session after processing
    }
}

function handleRetirementFund() {
    if (empty($_SESSION['data']['current_savings'])) {
        echo "What is your current savings amount?";
        $_SESSION['state'] = 'retirement_fund';
        $_SESSION['current_question'] = 'current_savings';
    } elseif (empty($_SESSION['data']['monthly_deposit'])) {
        echo "What is the monthly saving amount for retirement?";
        $_SESSION['current_question'] = 'monthly_deposit';
    } elseif (empty($_SESSION['data']['rate'])) {
        echo "What is the interest rate?";
        $_SESSION['current_question'] = 'rate';
    } elseif (empty($_SESSION['data']['time'])) {
        echo "What is the time period until retirement (in years)?";
        $_SESSION['current_question'] = 'time';
    } else {
        $currentSavings = $_SESSION['data']['current_savings'];
        $monthlyDeposit = $_SESSION['data']['monthly_deposit'];
        $rate = $_SESSION['data']['rate'];
        $time = $_SESSION['data']['time'];
        $command = "swipl -q -s financial_assistant.pl -g \"run_retirement_fund($currentSavings, $monthlyDeposit, $rate, $time), halt.\"";
        $retirementFund = shell_exec($command);
        echo $retirementFund;
        initializeSession(); // Reset session after processing
    }
}

// Function to handle currency conversion
function handleCurrencyConversion() {
    if (empty($_SESSION['data']['currency_amount'])) {
        echo "How much would you like to convert?";
        $_SESSION['state'] = 'currency_conversion';
        $_SESSION['current_question'] = 'currency_amount';
    } elseif (empty($_SESSION['data']['from_currency'])) {
        echo "Which currency are you converting from (e.g., USD)?";
        $_SESSION['current_question'] = 'from_currency';
    } elseif (empty($_SESSION['data']['to_currency'])) {
        echo "Which currency are you converting to (e.g., EUR)?";
        $_SESSION['current_question'] = 'to_currency';
    } else {
        $amount = $_SESSION['data']['currency_amount'];
        $fromCurrency = $_SESSION['data']['from_currency'];
        $toCurrency = $_SESSION['data']['to_currency'];
        $convertedAmount = convertCurrency($amount, $fromCurrency, $toCurrency);
        echo "$amount $fromCurrency is equal to $convertedAmount $toCurrency.";
        initializeSession(); // Reset session after processing
    }
}

// Function to handle stock price requests
function handleStockPrice() {
    if (empty($_SESSION['data']['stock_symbol'])) {
        echo "Which stock symbol would you like to check?";
        $_SESSION['state'] = 'stock_price';
        $_SESSION['current_question'] = 'stock_symbol';
    } else {
        $stockSymbol = $_SESSION['data']['stock_symbol'];
        $stockPrice = fetchStockPrice($stockSymbol);
        echo "The latest stock price for $stockSymbol is $stockPrice.";
        initializeSession(); // Reset session after processing
    }
}

// Function to detect if a new query is being initiated
function detectNewQuery($userInput) {
    $keywords = ['financial advice', 'stock price', 'simple interest', 'compound interest', 'emi', 'future savings', 'retirement fund', 'dti', 'currency conversion'];
    foreach ($keywords as $keyword) {
        if (stripos($userInput, $keyword) !== false) {
            return true; // A new query has been detected
        }
    }
    return false; // No new query detected
}

// Function to handle the help request
function handleHelp() {
    echo "Here are the available functions you can ask for:
    1. Financial advice - Get advice based on your income and expenses.
    2. Simple interest - Calculate simple interest based on principal, rate, and time.
    3. Compound interest - Calculate compound interest based on principal, rate, and time.
    4. EMI - Calculate your monthly EMI based on loan amount, interest rate, and time.
    5. Future savings - Calculate the future value of your savings based on monthly deposit, interest rate, and time.
    6. Retirement fund - Estimate your retirement fund based on your monthly savings, interest rate, and time until retirement.
    7. Debt-to-income (DTI) ratio - Calculate your debt-to-income ratio based on your debt and income.
    8. Currency conversion - Convert currency between two different currencies.
    9. Stock price - Get the latest price of a specific stock symbol.";
}

function processUserInput($userInput) {

    // Detect if a new query is being initiated
    $isNewQuery = detectNewQuery($userInput);

    if ($isNewQuery) {
        // Automatically reset the session and start processing the new query
        initializeSession();
    }

    if (stripos($userInput, "help") !== false) {
        handleHelp();
        return;
    }

    if (stripos($userInput, "financial advice") !== false) {
        $_SESSION['state'] = 'financial_advice';
        handleFinancialAdvice();
    } elseif (stripos($userInput, 'stock price') !== false || stripos($userInput, 'stock') !== false || stripos($userInput, 'share') !== false) {
        $_SESSION['state'] = 'stock_price';
        handleStockPrice();
    } elseif (stripos($userInput, 'simple interest') !== false) {
        $_SESSION['state'] = 'simple_interest';
        handleSimpleInterest();
    } elseif (stripos($userInput, 'compound interest') !== false) {
        $_SESSION['state'] = 'compound_interest';
        handleCompoundInterest();
    } elseif (stripos($userInput, 'emi') !== false || stripos($userInput, 'monthly installment') !== false) {
        $_SESSION['state'] = 'emi';
        handleEMI();
    } elseif (stripos($userInput, 'future savings') !== false || stripos($userInput, 'saving future') !== false) {
        $_SESSION['state'] = 'future_savings';
        handleFutureSavings();
    } elseif (stripos($userInput, 'retirement fund') !== false) {
        $_SESSION['state'] = 'retirement_fund';
        handleRetirementFund();
    } elseif (stripos($userInput, 'dti') !== false || stripos($userInput, 'debt-to-income') !== false) {
        $_SESSION['state'] = 'dti';
        handleDTI();
    } elseif (stripos($userInput, 'currency conversion') !== false || stripos($userInput, 'convert currency') !== false) {
        $_SESSION['state'] = 'currency_conversion';
        handleCurrencyConversion();
    } elseif ($_SESSION['state']) {
        // Handle user input based on current state
        $userInput = trim($userInput);
        $_SESSION['data'][$_SESSION['current_question']] = $userInput;

        // Handle the next step based on state
        switch ($_SESSION['state']) {
            case'financial_advice':
                handleFinancialAdvice();
                break;
            case 'simple_interest':
                handleSimpleInterest();
                break;
            case 'compound_interest':
                handleCompoundInterest();
                break;
            case 'emi':
                handleEMI();
                break;
            case 'dti':
                handleDTI();
                break;
            case 'future_savings':
                handleFutureSavings();
                break;
            case 'retirement_fund':
                handleRetirementFund();
                break;
            case 'currency_conversion':
                handleCurrencyConversion();
                break;
            case 'stock_price':
                handleStockPrice();
                break;
        }
    } else {
        echo "Please ask a financial question to start.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userInput = $_POST['input'] ?? '';
    if (!isset($_SESSION['state'])) {
        initializeSession(); // Initialize session on first request
    }

    processUserInput($userInput);
} else {
    echo "No input received.";
}
?>

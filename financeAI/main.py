import requests
import nltk
from nltk.tokenize import word_tokenize

# Download NLTK data
nltk.download('punkt')

# Function to fetch current stock price
def get_stock_price(symbol, api_key):
    url = f'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol={symbol}&apikey={api_key}'
    try:
        response = requests.get(url)
        data = response.json()
        price = data['Global Quote']['05. price']
        return float(price)
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return None

# Function to calculate compound interest
def calculate_compound_interest(principal, rate, time):
    amount = principal * (1 + rate / 100) ** time
    interest = amount - principal
    return amount, interest

# Function to calculate simple interest
def calculate_simple_interest(principal, rate, time):
    interest = (principal * rate * time) / 100
    amount = principal + interest
    return amount, interest

# Function to convert currency using ExchangeRate-API
def convert_currency(amount, from_currency, to_currency, api_key):
    url = f'https://v6.exchangerate-api.com/v6/{api_key}/pair/{from_currency}/{to_currency}/{amount}'
    try:
        response = requests.get(url)
        data = response.json()
        if data['result'] == 'success':
            converted_amount = data['conversion_result']
            return converted_amount
        else:
            print(f"Error: {data['error-type']}")
            return None
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return None

# Function to determine user intent
def determine_intent(message):
    tokens = word_tokenize(message.lower())
    if 'stock' in tokens and ('price' in tokens or 'prices' in tokens):
        return 'get_stock_price'
    elif 'compound' in tokens and 'interest' in tokens:
        return 'calculate_compound_interest'
    elif 'simple' in tokens and 'interest' in tokens:
        return 'calculate_simple_interest'
    elif 'convert' in tokens and 'currency' in tokens:
        return 'convert_currency'
    elif 'help' in tokens:
        return 'help'
    else:
        return 'unknown'

# Function to print help information
def print_help():
    print("Available commands:")
    print("- Get stock price: 'What is the current price of [symbol]?'")
    print("- Calculate compound interest: 'Calculate compound interest'")
    print("- Calculate simple interest: 'Calculate simple interest'")
    print("- Convert currency: 'Convert [amount] from [currency1] to [currency2]'")
    print("- Help: 'Help'")

# Main interaction loop
if __name__ == "__main__":
    api_key_stock = 'your stock api key'  # Replace with your Alpha Vantage API key
    api_key_currency = 'your current rate api key'  # Replace with your ExchangeRate-API key

    while True:
        user_input = input('How can I assist you? ')

        intent = determine_intent(user_input)

        if intent == 'get_stock_price':
            stock_symbol = input('Which stock symbol? ')
            price = get_stock_price(stock_symbol, api_key_stock)
            if price is not None:
                print(f'Current price of {stock_symbol}: ${price:.2f}')
        elif intent == 'calculate_compound_interest':
            principal = float(input('Enter principal amount: '))
            annual_rate = float(input('Enter annual interest rate: '))
            years = int(input('Enter number of years: '))

            final_amount, interest = calculate_compound_interest(principal, annual_rate, years)
            print(f'After {years} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})')
        elif intent == 'calculate_simple_interest':
            principal = float(input('Enter principal amount: '))
            annual_rate = float(input('Enter annual interest rate: '))
            years = int(input('Enter number of years: '))

            final_amount, interest = calculate_simple_interest(principal, annual_rate, years)
            print(f'After {years} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})')
        elif intent == 'convert_currency':
            amount = float(input('Enter amount: '))
            from_currency = input('Enter from currency (e.g., USD): ')
            to_currency = input('Enter to currency (e.g., EUR): ')

            converted_amount = convert_currency(amount, from_currency, to_currency, api_key_currency)
            if converted_amount is not None:
                print(f'{amount} {from_currency} is equivalent to {converted_amount:.2f} {to_currency}')
        elif intent == 'help':
            print_help()
        elif intent == 'unknown':
            print("I'm sorry, I don't understand. Can you please rephrase?")
        else:
            print("Sorry, I can't help with that.")

        if input('Do you have any other questions? (yes/no): ').lower() != 'yes':
            break

    print('Goodbye!')

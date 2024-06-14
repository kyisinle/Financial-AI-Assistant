import requests

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

# Example usage
def example_fetch_stock_price():
    stock_symbol = 'AAPL'
    api_key = 'MMA59VFVOI1Q68D8'  # Replace with your Alpha Vantage API key
    current_price = get_stock_price(stock_symbol, api_key)
    if current_price is not None:
        print(f'Current price of {stock_symbol}: ${current_price:.2f}')


# Step 3: Performing Financial Calculations

def calculate_compound_interest(principal, rate, time):
    amount = principal * (1 + rate / 100) ** time
    interest = amount - principal
    return amount, interest

# Example usage
def example_calculate_compound_interest():
    principal_amount = 1000
    annual_rate = 5
    years = 5

    final_amount, interest_earned = calculate_compound_interest(principal_amount, annual_rate, years)
    print(f'After {years} years at {annual_rate}% annual interest, '
          f'the final amount will be ${final_amount:.2f} (Interest earned: ${interest_earned:.2f})')


# Step 4: Implementing Natural Language Processing (Optional)

import nltk
from nltk.tokenize import word_tokenize

nltk.download('punkt')

# Function to determine user intent
def determine_intent(message):
    tokens = word_tokenize(message)
    if 'stock' in tokens and ('price' in tokens or 'prices' in tokens):
        return 'get_stock_price'
    elif 'compound' in tokens and 'interest' in tokens:
        return 'calculate_compound_interest'
    else:
        return 'unknown'

# Example usage
def example_determine_intent():
    user_message = "What is the current price of AAPL?"
    intent = determine_intent(user_message)
    print(f'User intent: {intent}')


# Step 5: Putting It All Together

if __name__ == "__main__":
    while True:
        user_input = input('How can I assist you? ')

        intent = determine_intent(user_input)

        if intent == 'get_stock_price':
            stock_symbol = input('Which stock symbol? ')
            api_key = 'your_alpha_vantage_api_key'  # Replace with your Alpha Vantage API key
            price = get_stock_price(stock_symbol, api_key)
            if price is not None:
                print(f'Current price of {stock_symbol}: ${price:.2f}')
        elif intent == 'calculate_compound_interest':
            principal = float(input('Enter principal amount: '))
            annual_rate = float(input('Enter annual interest rate: '))
            years = int(input('Enter number of years: '))

            final_amount, interest = calculate_compound_interest(principal, annual_rate, years)
            print(f'After {years} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})')
        elif intent == 'unknown':
            print("I'm sorry, I don't understand. Can you please rephrase?")
        else:
            print("Sorry, I can't help with that.")

        if input('Do you have any other questions? (yes/no): ').lower() != 'yes':
            break

    print('Goodbye!')

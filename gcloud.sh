#!/bin/bash

source .env

case $1 in
    deploy-function)
        gcloud functions deploy $FUNCTION_NAME \
            --region $REGION \
            --gen2 \
            --runtime php82 \
            --source ./src \
            --trigger-http \
            --entry-point $FUNCTION_NAME \
            --allow-unauthenticated \
            --memory 256Mi \
            --timeout 60s \
            --min-instances 1 \
            --max-instances 5 \
            --set-env-vars SLACK_WEBHOOK_URL=$SLACK_WEBHOOK_URL \
            --set-secrets PASSKIT_API_KEY=PASSKIT_API_KEY:latest,PASSKIT_API_SECRET=PASSKIT_API_SECRET:latest
        ;;
    delete-function)
        gcloud functions delete $FUNCTION_NAME \
            --region $REGION
        ;;
    run-local)
        FUNCTION_TARGET=$FUNCTION_NAME \
        SLACK_WEBHOOK_URL=$SLACK_WEBHOOK_URL \
        PASSKIT_API_KEY=$PASSKIT_API_KEY \
        PASSKIT_API_SECRET=$PASSKIT_API_SECRET \
        composer start-redeem-ticket \
            --working-dir=./src
        ;;
    *)
        echo "Invalid command : $1"
        ;;
esac